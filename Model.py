import numpy as np
import pandas as pd
import tensorflow as tf
from tensorflow import keras
from sklearn.preprocessing import StandardScaler, MinMaxScaler
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import OneHotEncoder
pd.set_option("display.precision", 1)

toys = pd.read_csv("LDDiagrams/toys.csv")
children = pd.read_csv("LDDiagrams/children.csv")

# Select relevant features
toy_features = toys[["category","age","gender","cognitive_development","motor_skills_development", "social_development", "emotional_development", "language_and_literacy"]]
child_features = children[["child_age","child_gender","child_preferred_category","child_CognitiveDevelopment","child_MotorSkillsDevelopment","child_SocialDevelopment","child_EmotionalDevelopment","child_Language_and_Literacy"]]

# One-hot encode categorical features
encoder = OneHotEncoder(handle_unknown='ignore', sparse_output=False)
# Encode "category" and "age" and "gender" features
category_encoded = encoder.fit_transform(toy_features[["category"]])
age_encoded = encoder.fit_transform(toy_features[["age"]])
gender_encoded = encoder.fit_transform(toy_features[["gender"]])

#Encode "child_age" and "child_gender" and "child_preferred_category features 
child_age_encoded = encoder.fit_transform(child_features[["child_age"]])
child_gender_encoded = encoder.fit_transform(child_features[["child_gender"]])
child_preferred_category_encoded = encoder.fit_transform(child_features[["child_preferred_category"]])

category_encoded_df = pd.DataFrame(category_encoded)
age_encoded_df = pd.DataFrame(age_encoded)
gender_encoded_df = pd.DataFrame(gender_encoded)

toy_features = pd.concat([category_encoded_df, age_encoded_df, gender_encoded_df, toy_features.iloc[:, 3:]], axis=1)

# Similarly for child_features
child_age_encoded_df = pd.DataFrame(child_age_encoded)
child_gender_encoded_df = pd.DataFrame(child_gender_encoded)
child_preferred_category_encoded_df = pd.DataFrame(child_preferred_category_encoded)

child_features = pd.concat([child_age_encoded_df, child_gender_encoded_df, child_preferred_category_encoded_df, child_features.iloc[:, 3:]], axis=1)

toy_features.columns = toy_features.columns.astype(str)
child_features.columns = child_features.columns.astype(str)

# Output preprocessed toy features to CSV
toy_features.to_csv("toy_features_preprocessed.csv", index=False)

# Output preprocessed child features to CSV
child_features.to_csv("child_features_preprocessed.csv", index=False)


# Split data into separate training sets for toys and child models
X_train_toys, X_test_toys, y_train_toys, y_test_toys = train_test_split(toy_features, toy_features, test_size=0.2, random_state=42)  # Use toy_features for both input and output
X_train_child, X_test_child, y_train_child, y_test_child = train_test_split(child_features, child_features, test_size=0.2, random_state=42)  # Use child_features for both input and output

toys_model = keras.Sequential([
    keras.layers.Dense(128, activation='relu'),
    keras.layers.Dense(64, activation='relu'),
    keras.layers.Dense(46)  # Output layer matching the number of toy features
])

child_model = keras.Sequential([
    keras.layers.Dense(128, activation='relu'),
    keras.layers.Dense(64, activation='relu'),
    keras.layers.Dense(37)  # Output layer matching the number of child features
])

# Train the toys model
toys_model.compile(optimizer='adam', loss='mean_squared_error')
toys_model.fit(X_train_toys, y_train_toys, epochs=100, validation_data=(X_test_toys, y_test_toys))

# Train the child model
child_model.compile(optimizer='adam', loss='mean_squared_error')
child_model.fit(X_train_child, y_train_child, epochs=100, validation_data=(X_test_child, y_test_child))

toy_vectors = toys_model.predict(toy_features)
child_vector = child_model.predict(child_features)

def dot_product(child_vector, toy_vector):
    return np.dot(child_vector, toy_vector)

similarity_scores = [dot_product(child_vector, toy_vector) for toy_vector in toy_vectors]

top_recommendations = np.argsort(-similarity_scores)[:5]  # Top 5 recommendations

recommended_toys = toys.iloc[top_recommendations]


