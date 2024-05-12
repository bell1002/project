import mysql.connector
import datetime
import xgboost as xgb
import numpy as np
from flask import Flask, request, jsonify

app = Flask(__name__)

# Load XGBoost model
model = xgb.Booster()
model.load_model('room_price_xgboost_model.json')

# Function to connect to MySQL database
def connect_to_mysql():
    try:
        connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="booking_hotel"
        )
        print("Connected to MySQL database successfully")
        return connection
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None

# Function to get room info from MySQL database
def get_room_info_from_database(room_id, connection):
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT * FROM rooms WHERE id = %s", (room_id,))
        room_info = cursor.fetchone()
        cursor.close()
        return room_info
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None

# Function to preprocess input data
def preprocess_input(room_id, connection):
    input_features = []
    room_info = get_room_info_from_database(room_id, connection)
    if room_info:
        input_features.append(room_info['price'])
        input_features.append(room_info['amenities'])
        input_features.append(room_info['name'])
    else:
        print("Không thể lấy thông tin phòng từ cơ sở dữ liệu")
        return None
    
   
   # Đọc dữ liệu từ yêu cầu HTTP và gán vào các biến
    checkin_date = request.json.get('checkin_date')
    checkout_date = request.json.get('checkout_date')
    adults = int(request.json.get('adults'))
    children = int(request.json.get('children'))

    # Kiểm tra xem dữ liệu đã được nhập hay chưa
    if not (checkin_date and checkout_date and adults and children):
        print("Dữ liệu nhập không đủ")
        return None

    # Chuyển đổi định dạng ngày tháng
    checkin_date = datetime.datetime.strptime(checkin_date, '%d-%m-%Y')
    checkout_date = datetime.datetime.strptime(checkout_date, '%d-%m-%d')


    input_features.append(checkin_date.day)
    input_features.append(checkin_date.month)
    input_features.append(checkout_date.day)
    input_features.append(checkout_date.month)
    input_features.append(adults)
    input_features.append(children)
    
    return np.array([input_features])

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    if 'room_id' in data:
        connection = connect_to_mysql()
        if connection:
            input_features = preprocess_input(data['room_id'], connection)
            if input_features:
                prediction = model.predict(xgb.DMatrix(input_features))
                connection.close()
                return jsonify({'predicted_price': prediction.tolist()})
            else:
                connection.close()
                return jsonify({'error': 'Không thể chuẩn bị dữ liệu'}), 500
        else:
            return jsonify({'error': 'Không thể kết nối đến cơ sở dữ liệu'}), 500
    else:
        return jsonify({'error': 'Dữ liệu không chứa khóa "room_id"'}), 400

if __name__ == '__main__':
    app.run(debug=True)
