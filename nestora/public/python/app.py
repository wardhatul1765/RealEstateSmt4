import os
import joblib
import numpy as np
import pandas as pd # <-- IMPORT PANDAS
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app) # Aktifkan CORS untuk semua route

# Tentukan path model relatif
# Pastikan app.py berada di folder yang benar relatif terhadap model, atau sesuaikan path ini.
# Jika app.py ada di 'public/python' dan model juga di sana:
model_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'rf_model.pkl')

# Cek jika file model ada
if not os.path.exists(model_path):
    print(f"Model tidak ditemukan di path: {model_path}")
    model = None
else:
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
        return jsonify({'error': 'Model not loaded properly', 'message': 'Model tidak berhasil dimuat di server.'}), 500

    try:
        data = request.json
        print(f"Data diterima untuk prediksi: {data}")

        # Daftar nama fitur HARUS SAMA PERSIS DENGAN SAAT TRAINING MODEL
        # Urutan di sini juga penting jika Anda membuat DataFrame dari dictionary
        # yang tidak terurut (Python < 3.7) atau untuk kejelasan.
        feature_names = [
            'bedrooms', 'bathrooms', 'sizeMin', 'verified', 'furnishing',
            'listing_age_category', 'view_type', 'title_keyword'
        ]

        # Validasi field yang dibutuhkan
        required_fields_from_js = ['bathrooms', 'bedrooms', 'furnishing', 'sizeMin', 'verified' ,
                                   'listing_age_category', 'view_type', 'title_keyword']
        missing_fields = [field for field in required_fields_from_js if field not in data]
        if missing_fields:
            print(f"Field yang hilang dari request: {', '.join(missing_fields)}")
            return jsonify({'error': f'Missing field(s) in request: {", ".join(missing_fields)}', 'message': f'Field berikut tidak ada dalam permintaan: {", ".join(missing_fields)}'}), 400

        # Membuat dictionary untuk DataFrame, mengambil nilai dari 'data'
        # Pastikan kunci di 'data' sama dengan yang Anda harapkan dari JavaScript
        input_data_for_df = {}
        try:
            # Urutan pengambilan nilai dari 'data' tidak krusial di sini,
            # karena DataFrame akan dibuat dengan 'columns=feature_names'
            # yang akan menentukan urutan akhir.
            input_data_for_df['bedrooms'] = [float(data['bedrooms'])]
            input_data_for_df['bathrooms'] = [float(data['bathrooms'])]
            input_data_for_df['sizeMin'] = [float(data['sizeMin'])] # Ini adalah sqft
            input_data_for_df['verified'] = [int(data['verified'])]
            input_data_for_df['furnishing'] = [int(data['furnishing'])]
            input_data_for_df['listing_age_category'] = [int(data['listing_age_category'])]
            input_data_for_df['view_type'] = [int(data['view_type'])]
            input_data_for_df['title_keyword'] = [int(data['title_keyword'])]
        except ValueError as ve:
            print(f"ValueError saat konversi data: {str(ve)}. Data yang diterima: {data}")
            return jsonify({'error': f'Invalid value type: {str(ve)}', 'message': 'Tipe data tidak valid untuk salah satu field.'}), 400
        except KeyError as ke:
            print(f"KeyError, field tidak ditemukan di data: {str(ke)}. Data yang diterima: {data}")
            return jsonify({'error': f'Missing key in input: {str(ke)}', 'message': f'Kunci {str(ke)} tidak ditemukan pada data input.'}), 400


        # Membuat DataFrame dengan nama kolom dan urutan yang benar
        # 'columns=feature_names' memastikan urutan kolom sesuai dengan yang digunakan saat training
        features_df = pd.DataFrame(input_data_for_df, columns=feature_names)
        
        print(f"DataFrame fitur untuk model:\n{features_df}") # Log DataFrame
        
        # Lakukan prediksi menggunakan DataFrame
        prediction = model.predict(features_df)
        
        # prediction akan menjadi array (misalnya [2802112.73]), ambil elemen pertama
        predicted_value = prediction[0]
        print(f"Hasil prediksi: {predicted_value}")

        return jsonify({'prediction_result': predicted_value})

    except Exception as e:
        # Tangani exception umum lainnya
        print(f"Terjadi exception umum saat prediksi: {str(e)}")
        import traceback
        traceback.print_exc() # Cetak traceback untuk debugging lebih lanjut
        return jsonify({'error': 'An unexpected error occurred', 'message': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
