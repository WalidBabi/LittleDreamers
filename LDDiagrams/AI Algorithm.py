import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Load data
toys_df = pd.read_csv("LDDiagrams/Data/toys_description.csv")
children_df = pd.read_csv("LDDiagrams/Data/children.csv")

#- Index Model : Vector space model
class indexer:

    def __init__(self, documents_df):
      # Concatenate the features into a single text feature
        documents_df['combined_features'] = documents_df['age'].astype(str) + ' ' + documents_df['skill_development'] + ' ' + documents_df['play_pattern']+ ' ' + documents_df['gender']+ ' ' + documents_df['description']
        # Create a TF-IDF vectorizer
        self.tfidf_vectorizer = TfidfVectorizer()
        #-then finally, fit the documents and transform
        self._index = self.tfidf_vectorizer.fit_transform(documents_df['combined_features'])

    def getindex(self):
        return self._index

    def vectorize(self, sentence):
        if isinstance(sentence,str):
            qry=pd.DataFrame([{"text":sentence}])
        else:
            qry=sentence
        return self.tfidf_vectorizer.transform(qry['text'])

vsm = indexer(toys_df)

class Retriever:

    def retrieve(self, query_vec, index_model):
        cosine_similarities = cosine_similarity(query_vec, index_model.getindex())
        results = pd.DataFrame(
            [{'ID':i+1, 'score':cosine_similarities[0][i]}
            for i in range(len(cosine_similarities[0]))]
        ).sort_values(by=['score'], ascending=False)
        return results[results["score"]>0]
    
child_id = 10

# Assuming the DataFrame is named 'children_df'
# Accessing the specified columns for the child with the given ID
child_data = children_df.loc[child_id, ['age', 'interests_and_preferences', 'challenges_or_learning_needs','gender']]

# Convert the information to text without column names
child_data = f"{child_data['age']} {child_data['interests_and_preferences']} {child_data['challenges_or_learning_needs']} {child_data['gender']}"

chold_vector=vsm.vectorize(child_data)
rt = Retriever()
res = rt.retrieve(chold_vector,vsm)
print(res.head())
