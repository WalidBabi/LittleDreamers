import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from surprise import Reader, Dataset, SVD
from sklearn.preprocessing import OneHotEncoder
from sklearn.preprocessing import StandardScaler, MinMaxScaler

toys = pd.read_csv("LDDiagrams/toys.csv")
children = pd.read_csv("LDDiagrams/children.csv")

# Select relevant features
toy_features = toys[["category","gender","age","cognitive_development","motor_skills_development", "social_development", "emotional_development", "language_and_literacy"]]
child_features = children[["child_gender","child_preferred_category","child_age","child_CognitiveDevelopment","child_MotorSkillsDevelopment","child_SocialDevelopment","child_EmotionalDevelopment","child_Language_and_Literacy"]]

# One-hot encode categorical features
encoder = OneHotEncoder(handle_unknown='ignore', sparse_output=False)

category_encoded = encoder.fit_transform(toy_features[["category"]])
category_encoded_df = pd.DataFrame(category_encoded, columns=encoder.get_feature_names_out(["category"]))

# Encode "gender" feature for toys
gender_encoded = encoder.fit_transform(toy_features[["gender"]])
gender_encoded_df = pd.DataFrame(gender_encoded, columns=encoder.get_feature_names_out(["gender"]))

# Combine encoded features with the original dataframe
toy_features = pd.concat([category_encoded_df, gender_encoded_df, toy_features.iloc[:, 2:]], axis=1)

# Similarly for child features

child_preferred_category_encoded = encoder.fit_transform(child_features[["child_preferred_category"]])
child_preferred_category_encoded_df = pd.DataFrame(child_preferred_category_encoded, columns=encoder.get_feature_names_out(["child_preferred_category"]))

child_gender_encoded = encoder.fit_transform(child_features[["child_gender"]])
child_gender_encoded_df = pd.DataFrame(child_gender_encoded, columns=encoder.get_feature_names_out(["child_gender"]))

# Similarly for child features
child_features = pd.concat([child_preferred_category_encoded_df, child_gender_encoded_df, child_features.iloc[:, 2:]], axis=1)

toy_features.columns = toy_features.columns.astype(str)
child_features.columns = child_features.columns.astype(str)

# Initialize StandardScaler
scaler = StandardScaler()

# Scale numerical features for toy_features
toy_features[['age', 'cognitive_development', 'motor_skills_development', 'social_development', 'emotional_development', 'language_and_literacy']] = scaler.fit_transform(toy_features[['age', 'cognitive_development', 'motor_skills_development', 'social_development', 'emotional_development', 'language_and_literacy']])

# Scale numerical features for child_features
child_features[['child_age', 'child_CognitiveDevelopment', 'child_MotorSkillsDevelopment', 'child_SocialDevelopment', 'child_EmotionalDevelopment', 'child_Language_and_Literacy']] = scaler.fit_transform(child_features[['child_age', 'child_CognitiveDevelopment', 'child_MotorSkillsDevelopment', 'child_SocialDevelopment', 'child_EmotionalDevelopment', 'child_Language_and_Literacy']])

# Output preprocessed toy features to CSV
toy_features.to_csv("toy_features_preprocessed.csv", index=False)

# Output preprocessed child features to CSV
child_features.to_csv("child_features_preprocessed.csv", index=False)


def get_content_based_recommendations(child_id):
    # Extract relevant features for the given child_id
    child_features_input = child_features.loc[child_id, ['child_age', 'child_CognitiveDevelopment', 'child_MotorSkillsDevelopment', 'child_SocialDevelopment', 'child_EmotionalDevelopment', 'child_Language_and_Literacy']]

    # Scale the child features using the same scaler used for toy features
    child_features_input_scaled = scaler.transform(child_features_input.values.reshape(1, -1))

    # Select the same features from toy dataset
    relevant_toy_features = toy_features[['age', 'cognitive_development', 'motor_skills_development', 'social_development', 'emotional_development', 'language_and_literacy']]

    # Calculate cosine similarity
    similarity = cosine_similarity(child_features_input_scaled, relevant_toy_features)

    # Get the indices of the top 10 recommendations
    recommended_toy_indices = np.argsort(-similarity[0])[:10]

    # Get the corresponding toy IDs
    recommended_toy_ids = toys.iloc[recommended_toy_indices].index.tolist()

    # Return recommended toy IDs
    return recommended_toy_ids

child_id = 0  # Example child ID
recommended_toy_ids = get_content_based_recommendations(child_id)

# Print names of recommended toys
recommended_toy_names = toys.loc[recommended_toy_ids, 'name'].tolist()
print("Recommended Toys:")
print(recommended_toy_names)
