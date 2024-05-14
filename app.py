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

def get_amenity_name_from_database(amenity_id, connection):
    try:
        cursor = connection.cursor()
        cursor.execute("SELECT name FROM amenities WHERE id = %s", (amenity_id,))
        amenity_name = cursor.fetchone()
        cursor.close()
        if amenity_name:
            return amenity_name[0]  # Trả về tên amenity
        else:
            print(f"Không tìm thấy tên amenity cho id {amenity_id}")
            return None
    except mysql.connector.Error as err:
        print(f"Lỗi khi truy vấn tên amenity: {err}")
        return None


def one_hot_encode(text):
    # Define your one-hot encoding logic here
    # This is just a simple example
    encoding = [0] * 26  # Assuming only lowercase letters are present
    for char in text:
        if char.islower():
            index = ord(char) - ord('a')
            encoding[index] = 1
    return encoding

def preprocess_input(room_id, connection, checkin_date, checkout_date, adults, children):
    input_features = []
    room_info = get_room_info_from_database(room_id, connection)
    if room_info:
        input_features.extend(one_hot_encode(room_info['name']))
        input_features.append(float(room_info['price']))  # Thêm giá trị của trường price
        
        # Lấy thông tin về amenities từ bảng amenities thông qua id
        amenities_ids = room_info['amenities'].split(',')
        for amenity_id in amenities_ids:
            amenity_name = get_amenity_name_from_database(amenity_id.strip(), connection)
            if amenity_name:
                # Mã hóa one-hot cho tên tiện ích và thêm vào input_features
                encoded_amenity = one_hot_encode(amenity_name)
                input_features.extend(encoded_amenity)
            else:
                print(f"Không tìm thấy tên amenities cho id {amenity_id}")
                return None

    else:
        print("Không thể lấy thông tin phòng từ cơ sở dữ liệu")
        return None
    
    # Chuyển đổi ngày nhận phòng và ngày trả phòng thành đối tượng datetime
    try:
        checkin_date = datetime.datetime.strptime(checkin_date, '%d/%m/%Y')
        checkout_date = datetime.datetime.strptime(checkout_date, '%d/%m/%Y')
    except ValueError:
        print("Ngày không hợp lệ")
        return None
    
    # Chuyển đổi số lượng người lớn và trẻ em thành số nguyên
    try:
        adults = int(adults)
        children = int(children)
    except ValueError:
        print("Số lượng người không hợp lệ")
        return None

    # Thêm các giá trị đã chuyển đổi vào danh sách đặc trưng
    input_features.append(checkin_date.day)
    input_features.append(checkin_date.month)
    input_features.append(checkout_date.day)
    input_features.append(checkout_date.month)
    input_features.append(adults)
    input_features.append(children)

    print("Input Features:", input_features)  # In ra các input_features để kiểm tra

    return input_features

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    print("Received data:", data)
    if 'room_id' in data:
        connection = connect_to_mysql()
        if connection:
            input_features = preprocess_input(data['room_id'], connection, data['checkin_date'], data['checkout_date'], data['adults'], data['children'])
            if input_features:
                prediction = model.predict(xgb.DMatrix(np.array([input_features], dtype=np.int32)))
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



