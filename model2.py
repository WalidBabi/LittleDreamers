import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans

# Load the dataset
df = pd.read_csv('LDDiagrams/toys.csv')

# Create a TF-IDF vectorizer
tfidf = TfidfVectorizer(stop_words='english')

# Replace NaN values with an empty string
df['name'] = df['name'].fillna('')

# Compute the TF-IDF matrix
tfidf_matrix = tfidf.fit_transform(df['name'])

# Compute the K-means clustering
kmeans = KMeans(n_clusters=5)
kmeans.fit(tfidf_matrix)

# Get the cluster labels
labels = kmeans.labels_

# Add the cluster labels to the dataframe
df['cluster'] = labels

# Define a function to get recommendations
def get_recommendations(title, df=df):
    # Get the cluster of the movie that matches the title
    cluster = df[df['title'] == title]['cluster'].iloc[0]

    # Get all the movies in the same cluster
    movies = df[df['cluster'] == cluster]

    # Sort the movies based on their similarity to the input movie
    movies = movies.sort_values('similarity', ascending=False)

    # Return the top 10 most similar movies
    return movies['title'].iloc[1:11]
