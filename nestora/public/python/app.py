import os
import joblib
import numpy as np
from flask import Flask, request, jsonify

app = Flask(__name__)

# Tentukan path model relatif berdasarkan folder 'public/python'
model_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'rf_model.pkl')

# Cek jika file model ada di path yang benar
if not os.path.exists(model_path):
    print(f"Model tidak ditemukan di path: {model_path}")

# Load model saat aplikasi dijalankan
try:
    with open(model_path, 'rb') as f:
        model = joblib.load(f)
except Exception as e:
    print(f"Error loading model: {str(e)}")
    model = None

@app.route('/prediksi/create', methods=['POST'])
def predict():
    if model is None:
        return jsonify({'error': 'Model not loaded properly'}), 500

    try:
        data = request.json
        
        required_fields = ['bathrooms', 'bedrooms', 'furnishing', 'sizeMin', 'verified' ,
                           'listing_age_category', 'view_type', 'title_keyword']
        for field in required_fields:
            if field not in data:
                return jsonify({'error': f'Missing field: {field}'}), 400

        bathrooms = float(data['bathrooms'])
        bedrooms = float(data['bedrooms'])
        furnishing = int(data['furnishing'])
        sizeMin = float(data['sizeMin'])
        verified = int(data['verified'])    
        listing_age_category = int(data['listing_age_category'])
        view_type = int(data['view_type'])
        title_keyword = int(data['title_keyword'])

        features = np.array([[bathrooms, bedrooms, furnishing, sizeMin,verified, 
                              listing_age_category, view_type, title_keyword]])

        prediction = model.predict(features)

        return jsonify({'prediction_result': prediction[0]})

    except ValueError as ve:
        return jsonify({'error': f'Invalid value type: {str(ve)}'}), 400
    except KeyError as ke:
        return jsonify({'error': f'Missing key in input: {str(ke)}'}), 400
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
