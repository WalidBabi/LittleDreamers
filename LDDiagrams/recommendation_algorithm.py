import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import sys
import json
# Index Model: Vector space model
class Indexer:

    def __init__(self, documents_df):
        # Concatenate the features into a single text feature
        documents_df['combined_features'] = documents_df['age'].astype(str) + ' ' + documents_df['skill_development'] + ' ' + documents_df['play_pattern'] + ' ' + documents_df['gender'] + ' ' + documents_df['description']
        # Create a TF-IDF vectorizer
        self.tfidf_vectorizer = TfidfVectorizer()
        # Fit the documents and transform
        self._index = self.tfidf_vectorizer.fit_transform(documents_df['combined_features'])

    def get_index(self):
        return self._index

    def vectorize(self, sentence):
        if isinstance(sentence, str):
            qry = pd.DataFrame([{"text": sentence}])
        else:
            qry = sentence
        return self.tfidf_vectorizer.transform(qry['text'])


class Retriever:

    def retrieve(self, query_vec, index_model):
        cosine_similarities = cosine_similarity(query_vec, index_model.get_index())
        results = pd.DataFrame(
            [{'ID': i + 1, 'score': cosine_similarities[0][i]}
             for i in range(len(cosine_similarities[0]))]
        ).sort_values(by=['score'], ascending=False)
        return results[results["score"] > 0]


# Load child data from JSON input
def load_child_data():
    child_data = sys.argv[1]
    return child_data

# Main function to execute recommendation algorithm

def main():
    # Load toy descriptions data from file
    with open(sys.argv[2], 'r') as f:
        toys_data = json.load(f)
    # Load toys data into a DataFrame
    toys_df = pd.DataFrame(toys_data)

    # Create indexer and retriever objects
    vsm = Indexer(toys_df)
    rt = Retriever()

    # Process child data (if needed)
    child_data = load_child_data()
    child_vector = vsm.vectorize(child_data)

    # Retrieve toy recommendations
    recommendations = rt.retrieve(child_vector, vsm)
    print(recommendations.head())

if __name__ == "__main__":
    main()