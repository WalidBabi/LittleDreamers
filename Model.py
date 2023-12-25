import numpy as np
import pandas as pd
import tensorflow as tf
from tensorflow import keras
from sklearn.preprocessing import StandardScaler, MinMaxScaler
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import OneHotEncoder
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.metrics import precision_score, recall_score
import matplotlib.pyplot as plt
from keras.callbacks import EarlyStopping
pd.set_option("display.precision", 1)

toys = pd.read_csv("LDDiagrams/toys.csv")
children = pd.read_csv("LDDiagrams/children.csv")

# Select relevant features
toy_features = toys[["category","age","gender","cognitive_development","motor_skills_development", "social_development", "emotional_development", "language_and_literacy"]]
child_features = children[["child_age","child_gender","child_preferred_category","child_CognitiveDevelopment","child_MotorSkillsDevelopment","child_SocialDevelopment","child_EmotionalDevelopment","child_Language_and_Literacy"]]

# One-hot encode categorical features
encoder = OneHotEncoder(handle_unknown='ignore', sparse_output=False)

category_encoded = encoder.fit_transform(toy_features[["category"]])
category_encoded_df = pd.DataFrame(category_encoded, columns=encoder.get_feature_names_out(["category"]))
# Encode "age" feature for toys
age_encoded = encoder.fit_transform(toy_features[["age"]])
age_encoded_df = pd.DataFrame(age_encoded, columns=encoder.get_feature_names_out(["age"]))

# Encode "gender" feature for toys
gender_encoded = encoder.fit_transform(toy_features[["gender"]])
gender_encoded_df = pd.DataFrame(gender_encoded, columns=encoder.get_feature_names_out(["gender"]))

# Combine encoded features with the original dataframe
toy_features = pd.concat([category_encoded_df,age_encoded_df, gender_encoded_df, toy_features.iloc[:, 3:]], axis=1)

# Similarly for child features

child_preferred_category_encoded = encoder.fit_transform(child_features[["child_preferred_category"]])
child_preferred_category_encoded_df = pd.DataFrame(child_preferred_category_encoded, columns=encoder.get_feature_names_out(["child_preferred_category"]))

child_age_encoded = encoder.fit_transform(child_features[["child_age"]])
child_age_encoded_df = pd.DataFrame(child_age_encoded, columns=encoder.get_feature_names_out(["child_age"]))

child_gender_encoded = encoder.fit_transform(child_features[["child_gender"]])
child_gender_encoded_df = pd.DataFrame(child_gender_encoded, columns=encoder.get_feature_names_out(["child_gender"]))

# Similarly for child features
child_features = pd.concat([child_preferred_category_encoded_df,child_age_encoded_df, child_gender_encoded_df, child_features.iloc[:, 3:]], axis=1)

toy_features.columns = toy_features.columns.astype(str)
child_features.columns = child_features.columns.astype(str)

# Output preprocessed toy features to CSV
toy_features.to_csv("toy_features_preprocessed.csv", index=False)

# Output preprocessed child features to CSV
child_features.to_csv("child_features_preprocessed.csv", index=False)


# Split data for toys into training and testing sets
X_train_toys, X_test_toys = train_test_split(toy_features, test_size=0.2, random_state=42)

# Use toy_features for input and output
y_train_toys, y_test_toys = X_train_toys, X_test_toys

# Split data for children into training and testing sets
X_train_child, X_test_child = train_test_split(child_features, test_size=0.2, random_state=42)
# Use child_features for input and output
y_train_child, y_test_child = X_train_child, X_test_child

scaler = StandardScaler()  # or MinMaxScaler()
X_train_toys_scaled = scaler.fit_transform(X_train_toys)
X_test_toys_scaled = scaler.transform(X_test_toys)

X_train_child_scaled = scaler.fit_transform(X_train_child)
X_test_child_scaled = scaler.transform(X_test_child)


toys_model = keras.Sequential([
    keras.layers.Dense(128, activation='relu'),
    keras.layers.Dropout(0.5),
    keras.layers.Dense(64, activation='relu'),
    keras.layers.Dropout(0.5),
    keras.layers.Dense(37)
])

child_model = keras.Sequential([
    keras.layers.Dense(128, activation='relu'),
    keras.layers.Dropout(0.5),
    keras.layers.Dense(64, activation='relu'),
    keras.layers.Dropout(0.5),
    keras.layers.Dense(37)
])

# Train the toys model
toys_model.compile(optimizer='adam', loss='mean_squared_error')

# Train the child model
child_model.compile(optimizer='adam', loss='mean_squared_error')

early_stopping = EarlyStopping(patience=10, restore_best_weights=True)

toys_model.fit(X_train_toys_scaled, y_train_toys, epochs=200, validation_data=(X_test_toys_scaled, y_test_toys), callbacks=[early_stopping])
child_model.fit(X_train_child_scaled, y_train_child, epochs=200, validation_data=(X_test_child_scaled, y_test_child), callbacks=[early_stopping])

toy_vector = toys_model.predict(toy_features)
child_vector = child_model.predict(child_features)
############################################################3
# Get a child's vector
test_child_index = 2  # Replace with the index of the test child
test_child_vector = child_vector[test_child_index]
# Assuming you have the children's data in a DataFrame named 'children'
test_child_data = children.iloc[test_child_index]

print(test_child_data)

# Calculate cosine similarity between the child's vector and all toy vectors
similarities = cosine_similarity(test_child_vector.reshape(1, -1), toy_vector)[0]

# Sort toys based on similarity scores (descending)
sorted_indices = np.argsort(similarities)[::-1]

# Select the top N recommended toys
num_recommendations = 10  # Adjust as needed
recommended_toy_indices = sorted_indices[:num_recommendations]

# Display recommended toys
for index in recommended_toy_indices:
    recommended_toy = toys.iloc[index]
    print("Recommended Toy:", recommended_toy["name"])  # Access other toy details as needed

# Assuming you have a list of relevant toy IDs for the test child
relevant_toys = [1,4,6,14,16,23,
28,
34,
43,
48
]  # Replace with actual relevant toy IDs

# Map the toy indices to toy IDs
recommended_toy_ids = toys.iloc[recommended_toy_indices]["id"].tolist()

# Calculate precision and recall
precision = precision_score(relevant_toys, recommended_toy_ids, average='micro')
recall = recall_score(relevant_toys, recommended_toy_ids, average='micro')

print("Precision:", precision)
print("Recall:", recall)

# Additional checks
if len(relevant_toys) == 0:
    print("Warning: No relevant toys provided.")
elif len(recommended_toy_ids) == 0:
    print("Warning: No toys recommended.")
# # Evaluate the toys model
# toys_loss = toys_model.evaluate(X_test_toys_scaled, y_test_toys)
# print(f"Toys Model - Test Loss: {toys_loss}")

# # Evaluate the child model
# child_loss = child_model.evaluate(X_test_child_scaled, y_test_child)
# print(f"Child Model - Test Loss: {child_loss}")

# # Visualize predictions for the toys model
# toys_predictions = toys_model.predict(X_test_toys_scaled)

# plt.scatter(y_test_toys, toys_predictions)
# plt.xlabel("Actual Values")
# plt.ylabel("Predicted Values")
# plt.title("Toys Model - Actual vs Predicted")
# plt.show()

# # Visualize predictions for the child model
# child_predictions = child_model.predict(X_test_child_scaled)

# plt.scatter(y_test_child, child_predictions)
# plt.xlabel("Actual Values")
# plt.ylabel("Predicted Values")
# plt.title("Child Model - Actual vs Predicted")
# plt.show()

# 6. User Input Handling:
# In a real-world application, you'll need a mechanism to take user input, preprocess it, and use the trained models for recommendations. This would involve loading the saved models and running predictions on the preprocessed input.


# 8. Model Deployment:
# Save the trained models and deploy them using frameworks like TensorFlow Serving or TensorFlow Lite for mobile applications.

# # Save models
# toys_model.save('toys_model.h5')
# child_model.save('child_model.h5')

# # Load models
# loaded_toys_model = keras.models.load_model('toys_model.h5')
# loaded_child_model = keras.models.load_model('child_model.h5')

# # Use models for predictions
# toy_vector = loaded_toys_model.predict(toy_features)
# child_vector = loaded_child_model.predict(child_features)