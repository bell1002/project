from flask import Flask, request, jsonify
import xgboost as xgb
import numpy as np
import logging

app = Flask(__name__)

# Khởi tạo logger
logging.basicConfig(level=logging.DEBUG)

# Giả sử bạn đã tải mô hình XGBoost đã huấn luyện
model = xgb.Booster()
model.load_model('room_price_xgboost_model.json')

# Tạo ánh xạ từ tên phòng thành giá trị số
room_name_mapping = {
    'Double Room': 4,
    'Single Room': 5,
    # Thêm các phòng khác nếu có
}

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json
    app.logger.debug('Received data: %s', data)  # Ghi log giá trị của biến data

    # Kiểm tra xem key 'features' có tồn tại trong dữ liệu hay không
    if 'features' not in data:
        app.logger.error('Key "features" không tồn tại trong dữ liệu: %s', data)
        return jsonify({'error': 'Key "features" không tồn tại trong dữ liệu'}), 400

    try:
        # Trích xuất các đặc trưng từ dữ liệu đầu vào
        features_dict = {
            'room_id': data['features']['room_id'],
            'checkin_date': data['features']['checkin_date'],
            'checkout_date': data['features']['checkout_date'],
            'adults': data['features']['adults'],
            'children': data['features']['children']
        }

        app.logger.debug('Processed features: %s', features_dict)

        # Tạo DMatrix từ từ điển đặc trưng
        features = np.array([
            int(features_dict['room_id']),
            int(features_dict['checkin_date']),
            int(features_dict['checkout_date']),
            int(features_dict['adults']),
            int(features_dict['children'])
        ]).reshape(1, -1)

        # In các tên đặc trưng và các giá trị để debug
        app.logger.debug('Feature names: %s', ['room_id', 'checkin_date', 'checkout_date', 'adults', 'children'])
        app.logger.debug('Feature values: %s', features)

        # Sử dụng từ điển đặc trưng thay vì mảng numpy
        dmatrix = xgb.DMatrix(features, feature_names=['room_id', 'checkin_date', 'checkout_date', 'adults', 'children'])
        prediction = model.predict(dmatrix)
        app.logger.debug('Prediction: %s', prediction)

        return jsonify({'prediction': prediction.tolist()})
    except KeyError as e:
        app.logger.error('Missing key in features: %s', e)
        return jsonify({'error': f'Missing key in features: {str(e)}'}), 400
    except ValueError as e:
        app.logger.error('Invalid value in features: %s', e)
        return jsonify({'error': f'Invalid value in features: {str(e)}'}), 400
    except Exception as e:
        app.logger.error('Error processing request: %s', e)
        return jsonify({'error': 'Error processing request'}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
