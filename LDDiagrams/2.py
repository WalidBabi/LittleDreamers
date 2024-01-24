import pandas as pd
from sklearn.preprocessing import OneHotEncoder
from sklearn.metrics.pairwise import cosine_similarity

# Load data
toys_df = pd.read_csv("LDDiagrams/toys1.csv")
children_df = pd.read_csv("LDDiagrams/children1.csv")

# Preprocess toy data
toys_df["Age"] = pd.to_numeric(toys_df["Age"])  # Ensure age is numeric
encoder = OneHotEncoder(handle_unknown='ignore')
toys_encoded = encoder.fit_transform(toys_df[["Skill Development", "Play Pattern"]])
toys_df = pd.concat([toys_df, pd.DataFrame(toys_encoded.toarray(), columns=encoder.get_feature_names_out())], axis=1)
toys_df = toys_df.drop(["Toy Category", "Skill Development", "Play Pattern"], axis=1)


# Preprocess child data
children_df["Age"] = pd.to_numeric(children_df["Age"])
encoder = OneHotEncoder(handle_unknown='ignore')
children_encoded = encoder.fit_transform(children_df[["Interests", "Challenges"]])
children_df = pd.concat([children_df, pd.DataFrame(children_encoded.toarray(), columns=encoder.get_feature_names_out())], axis=1)
children_df = children_df.drop(["Interests", "Challenges"], axis=1)

# Create matrices for similarity calculation
toys_matrix = toys_df.values
children_matrix = children_df.values
# Calculate cosine similarity between toys and children
similarity_matrix = cosine_similarity(children_matrix, toys_matrix)

# Function to recommend toys for a child
def recommend_toys(child_index, top_n=5):
    recommended_toy_indices = similarity_matrix[child_index].argsort()[-top_n:][::-1]
    recommended_toys = toys_df.iloc[recommended_toy_indices]
    return recommended_toys

# Example usage
child_index = 5  # Change to the desired child index
recommendations = recommend_toys(child_index)
print(recommendations)
