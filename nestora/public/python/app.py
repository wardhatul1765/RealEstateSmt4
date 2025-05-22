import os
import joblib
import numpy as np
from flask import Flask, request, jsonify
from flask_cors import CORS # <--- IMPORT BARU

app = Flask(__name__)
CORS(app) # <--- AKTIFKAN CORS UNTUK SEMUA ROUTE DI APLIKASI FLASK

# Tentukan path model relatif berdasarkan folder 'public/python'
# Pastikan path ini benar relatif terhadap lokasi app.py
# Jika app.py ada di public/python, maka __file__ akan mengarah ke sana.
model_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'rf_model.pkl')

# Cek jika file model ada di path yang benar
if not os.path.exists(model_path):
    print(f"Model tidak ditemukan di path: {model_path}")
    # Pertimbangkan untuk tidak melanjutkan jika model tidak ada, atau handle dengan baik
    model = None 
else:
    # Load model saat aplikasi dijalankan
    try:
        with open(model_path, 'rb') as f:
            model = joblib.load(f)
        print(f"Model berhasil di-load dari: {model_path}")
    except Exception as e:
        print(f"Error loading model: {str(e)}")
        model = None

@app.route('/prediksi/create', methods=['POST'])
def predict():
    if model is None:
        print("Model tidak di-load, request prediksi tidak dapat diproses.")
        return jsonify({'error': 'Model not loaded properly'}), 500

    try:
        data = request.json
        print(f"Data diterima untuk prediksi: {data}") # Tambahkan log untuk melihat data yang masuk
        
        required_fields = ['bathrooms', 'bedrooms', 'furnishing', 'sizeMin', 'verified' ,
                           'listing_age_category', 'view_type', 'title_keyword']
        missing_fields = [field for field in required_fields if field not in data]
        if missing_fields:
            print(f"Field yang hilang: {', '.join(missing_fields)}")
            return jsonify({'error': f'Missing field(s): {", ".join(missing_fields)}'}), 400

        # Konversi tipe data dengan hati-hati
        bathrooms = float(data['bathrooms'])
        bedrooms = float(data['bedrooms'])
        furnishing = int(data['furnishing'])
        sizeMin = float(data['sizeMin']) # Ini adalah sqft
        verified = int(data['verified'])    
        listing_age_category = int(data['listing_age_category'])
        view_type = int(data['view_type'])
        title_keyword = int(data['title_keyword'])

        features = np.array([[bedrooms, bathrooms, sizeMin, verified, furnishing,
                            listing_age_category, view_type, title_keyword]]) # <-- URUTAN SESUAI TRAINING
        
        print(f"Fitur untuk model: {features}")
        prediction = model.predict(features)
        print(f"Hasil prediksi: {prediction[0]}")

        return jsonify({'prediction_result': prediction[0]})

    except ValueError as ve:
        print(f"ValueError saat konversi data: {str(ve)}")
        return jsonify({'error': f'Invalid value type: {str(ve)}'}), 400
    except KeyError as ke:
        print(f"KeyError, field tidak ditemukan di data: {str(ke)}")
        return jsonify({'error': f'Missing key in input: {str(ke)}'}), 400
    except Exception as e:
        print(f"Terjadi exception umum: {str(e)}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    # Pastikan Flask berjalan di 0.0.0.0 agar bisa diakses dari luar container/VM jika perlu,
    # atau biarkan default 127.0.0.1 jika hanya untuk akses lokal.
    # Port 5000 adalah default Flask.
    app.run(debug=True, host='0.0.0.0', port=5000) # Menjalankan di port 5000 dan bisa diakses dari network