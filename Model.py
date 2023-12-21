import numpy as np
import numpy.ma as ma
import pandas as pd
import tensorflow as tf
import openpyxl
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

# # Save toy_features to Excel
# toy_features.to_excel("processed_toy_features.xlsx", index=False)  # Set index=False to omit row indices

# # Save child_features to Excel
# child_features.to_excel("processed_child_features.xlsx", index=False)

# print("Data saved to Excel files successfully!")


# Consider scaling or normalization if necessary
scaler = StandardScaler()  # Or MinMaxScaler()
toy_features = scaler.fit_transform(toy_features)
child_features = scaler.fit_transform(child_features)

# Split data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(toy_features, child_features, test_size=0.2, random_state=42)

# Build and train a model (e.g., a neural network)
model = keras.Sequential([
    keras.layers.Dense(64, activation='relu'),
    keras.layers.Dense(64, activation='relu'),
    keras.layers.Dense(4)  # Output layer matching number of features
])

model.compile(optimizer='adam', loss='mean_squared_error')
model.fit(X_train, y_train, epochs=100, validation_data=(X_test, y_test))

# Use the trained model to make recommendations
# ... (code for generating recommendations based on child's profile)


# print("First 5 rows of toys dataset:")
# print(toys.head())

# print("\nFirst 5 rows of children dataset:")
# print(children.head())
