import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Load data
toys_df = pd.read_csv("LDDiagrams/V2/toys.csv")
children_df = pd.read_csv("LDDiagrams/V2/childrin.csv")

#- Index Model : Vector space model
class indexer:

    def __init__(self, documents_df):
      # Concatenate the features into a single text feature
        documents_df['combined_features'] = documents_df['Toy Category'] + ' ' + documents_df['Age'].astype(str) + ' ' + documents_df['Skill Development'] + ' ' + documents_df['Play Pattern']
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
    
child_id = 4

    # Assuming the DataFrame is named 'children_df'
# Accessing the specified columns for the child with the given ID
child_data = children_df.loc[child_id, ['Age', 'Developmental_Stage', 'Interests_and_Preferences', 'Challenges_or_Learning_Needs']]

# Convert the information to text without column names
child_data = f"{child_data['Age']} {child_data['Developmental_Stage']} {child_data['Interests_and_Preferences']} {child_data['Challenges_or_Learning_Needs']}"


chold_vector=vsm.vectorize(child_data)
rt = Retriever()
res = rt.retrieve(chold_vector,vsm)
print(res.head())