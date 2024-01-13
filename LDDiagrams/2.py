import pandas as pd
from sklearn.preprocessing import OneHotEncoder, LabelEncoder
from sklearn.metrics.pairwise import cosine_similarity

# Load toy and children datasets
toys_df = pd.read_csv("LDDiagrams/toys1.csv")
child_data_df = pd.read_csv("LDDiagrams/children1.csv")

# Select a child's data (e.g., the first child)
child_index = 1
child_data = child_data_df.iloc[child_index].to_dict()
print(child_data)
# Preprocess toy data
toys_df["Age Range"] = pd.to_numeric(toys_df["Age Range"].str.split("-").str.get(0))

encoder = OneHotEncoder(handle_unknown='ignore')
toys_encoded = encoder.fit_transform(toys_df[["Skill Development", "Play Pattern"]])
toys_df = pd.concat([toys_df, pd.DataFrame(toys_encoded.toarray(), columns=encoder.get_feature_names_out())], axis=1)
toys_df = toys_df.drop(["Skill Development", "Play Pattern"], axis=1)

toys_df["Educational Value"] = LabelEncoder().fit_transform(toys_df["Educational Value"])
print(toys_df)
# Process child data
child_vector = []
child_vector.append(child_data["Age"])

encoder = OneHotEncoder(handle_unknown='ignore')
child_stage_encoded = encoder.fit_transform([[child_data["Developmental Stage"]]])
child_vector.extend(child_stage_encoded.toarray()[0])

child_vector.extend([1 if interest in toys_df.columns else 0 for interest in child_data["Interests and Preferences"].split(", ")])
child_vector.extend([1 if challenge in toys_df.columns else 0 for challenge in child_data["Challenges or Learning Needs"].split(", ")])

# Calculate cosine similarity
toy_vectors = toys_df.drop("Toy Category", axis=1).values

# Pad child vector with zeros if needed
child_vector = child_vector + [0] * (toy_vectors.shape[1] - len(child_vector))
# Save toy vectors to CSV
toy_vectors_df = pd.DataFrame(toy_vectors, columns=toys_df.columns.drop("Toy Category"))
toy_vectors_df.to_csv("LDDiagrams/toy_vectors.csv", index=False)

# Save child vectors to CSV
child_vectors_df = pd.DataFrame([child_vector], columns=toys_df.columns.drop("Toy Category"))
child_vectors_df.to_csv("LDDiagrams/child_vector.csv", index=False)
toy_similarities = cosine_similarity([child_vector], toy_vectors)[0]

# Recommend the top 3 toys
top_3_indices = toy_similarities.argsort()[-3:][::-1]
recommended_toys = toys_df.iloc[top_3_indices]["Toy Category"].tolist()

print("Recommended toys for the child:", recommended_toys)
