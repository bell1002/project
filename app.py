# # import mysql.connector
# # import datetime
# # import xgboost as xgb
# # import numpy as np
# # from flask import Flask, request, jsonify

# # app = Flask(__name__)

# # # Load XGBoost model
# # model = xgb.Booster()
# # model.load_model('room_price_xgboost_model.json')

# # # Function to connect to MySQL database
# # def connect_to_mysql():
# #     try:
# #         connection = mysql.connector.connect(
# #             host="localhost",
# #             user="root",
# #             password="",
# #             database="booking_hotel"
# #         )
# #         print("Connected to MySQL database successfully")
# #         return connection
# #     except mysql.connector.Error as err:
# #         print(f"Error: {err}")
# #         return None

# # # Function to get room info from MySQL database
# # def get_room_info_from_database(room_id, connection):
# #     try:
# #         cursor = connection.cursor(dictionary=True)
# #         cursor.execute("SELECT * FROM rooms WHERE id = %s", (room_id,))
# #         room_info = cursor.fetchone()
# #         cursor.close()
# #         return room_info
# #     except mysql.connector.Error as err:
# #         print(f"Error: {err}")
# #         return None

# # def get_amenity_name_from_database(amenity_id, connection):
# #     try:
# #         cursor = connection.cursor()
# #         cursor.execute("SELECT name FROM amenities WHERE id = %s", (amenity_id,))
# #         amenity_name = cursor.fetchone()
# #         cursor.close()
# #         if amenity_name:
# #             return amenity_name[0]  # Trả về tên amenity
# #         else:
# #             print(f"Không tìm thấy tên amenity cho id {amenity_id}")
# #             return None
# #     except mysql.connector.Error as err:
# #         print(f"Lỗi khi truy vấn tên amenity: {err}")
# #         return None


# # def one_hot_encode(text):
# #     # Define your one-hot encoding logic here
# #     # This is just a simple example
# #     encoding = [0] * 26  # Assuming only lowercase letters are present
# #     for char in text:
# #         if char.islower():
# #             index = ord(char) - ord('a')
# #             encoding[index] = 1
# #     return encoding

# # def preprocess_input(room_id, connection, checkin_date, checkout_date, adults, children):
# #     input_features = []
# #     room_info = get_room_info_from_database(room_id, connection)
# #     if room_info:
# #         input_features.extend(one_hot_encode(room_info['name']))
# #         input_features.append(float(room_info['price']))  # Thêm giá trị của trường price
        
# #         # Lấy thông tin về amenities từ bảng amenities thông qua id
# #         amenities_ids = room_info['amenities'].split(',')
# #         for amenity_id in amenities_ids:
# #             amenity_name = get_amenity_name_from_database(amenity_id.strip(), connection)
# #             if amenity_name:
# #                 # Mã hóa one-hot cho tên tiện ích và thêm vào input_features
# #                 encoded_amenity = one_hot_encode(amenity_name)
# #                 input_features.extend(encoded_amenity)
# #             else:
# #                 print(f"Không tìm thấy tên amenities cho id {amenity_id}")
# #                 return None

# #     else:
# #         print("Không thể lấy thông tin phòng từ cơ sở dữ liệu")
# #         return None
    
# #     # Chuyển đổi ngày nhận phòng và ngày trả phòng thành đối tượng datetime
# #     try:
# #         checkin_date = datetime.datetime.strptime(checkin_date, '%d/%m/%Y')
# #         checkout_date = datetime.datetime.strptime(checkout_date, '%d/%m/%Y')
# #     except ValueError:
# #         print("Ngày không hợp lệ")
# #         return None
    
# #     # Chuyển đổi số lượng người lớn và trẻ em thành số nguyên
# #     try:
# #         adults = int(adults)
# #         children = int(children)
# #     except ValueError:
# #         print("Số lượng người không hợp lệ")
# #         return None

# #     # Thêm các giá trị đã chuyển đổi vào danh sách đặc trưng
# #     input_features.append(checkin_date.day)
# #     input_features.append(checkin_date.month)
# #     input_features.append(checkout_date.day)
# #     input_features.append(checkout_date.month)
# #     input_features.append(adults)
# #     input_features.append(children)

# #     print("Input Features:", input_features)  # In ra các input_features để kiểm tra

# #     return input_features

# # @app.route('/predict', methods=['POST'])
# # def predict():
# #     try:
# #         input_data = request.json
# #         if not input_data:
# #             return jsonify({'error': 'No input data provided'}), 400

# #         # Chuyển đổi dữ liệu đầu vào thành numpy array
# #         try:
# #             dmatrix = xgb.DMatrix(np.array([input_data]))
# #         except Exception as e:
# #             return jsonify({'error': f"Error creating DMatrix: {e}"}), 400

# #         # Dự đoán
# #         try:
# #             prediction = model.predict(dmatrix)
# #             return jsonify(prediction.tolist())
# #         except Exception as e:
# #             return jsonify({'error': f"Error making prediction: {e}"}), 500

# #     except Exception as e:
# #         return jsonify({'error': f"Unexpected error: {e}"}), 500


# # if __name__ == '__main__':
# #     app.run(debug=True)


# from flask import Flask, request, jsonify
# import xgboost as xgb
# import numpy as np
# import datetime
# import logging

# app = Flask(__name__)


# # Load XGBoost model
# model = xgb.Booster()
# model.load_model('room_price_xgboost_model.json')

# # Function to preprocess input data
# def preprocess_input(data):
#     try:
#         room_id = int(data.get('room_id'))
#         checkin_date = datetime.datetime.strptime(data.get('checkin_date'), '%Y-%m-%d')
#         checkout_date = datetime.datetime.strptime(data.get('checkout_date'), '%Y-%m-%d')
#         adults = int(data.get('adults'))
#         children = int(data.get('children'))

#         # Calculate duration of stay
#         duration = (checkout_date - checkin_date).days

#         # Return preprocessed input features as a list
#         return [room_id, checkin_date.day, checkin_date.month, checkout_date.day, checkout_date.month, adults, children, duration]

#     except Exception as e:
#         print(f"Error preprocessing input data: {e}")
#         return None

# @app.route('/predict', methods=['POST'])
# def predict():
#     try:
#         input_data = request.json
#         print('receive data:', input_data)
#         if not input_data:
#             return jsonify({'error': 'No input data provided'}), 400

#     #     # Preprocess input data
#     #     input_features = preprocess_input(input_data)
#     #     if input_features is None:
#     #         return jsonify({'error': 'Invalid input data format'}), 400

#     #     # Convert input features to XGBoost DMatrix
#     #     dmatrix = xgb.DMatrix(np.array([input_features]))

#     #     # Make prediction
#     #     prediction = model.predict(dmatrix)

#     #     # Return prediction as JSON response
#     #     return jsonify({'prediction': prediction.tolist()})

#     except Exception as e:
#         return jsonify({'error': f"Unexpected error: {e}"}), 500

# if __name__ == '__main__':
#     app.run(debug=True)




# from flask import Flask, request, jsonify
# import xgboost as xgb
# import numpy as np
# import logging

# app = Flask(__name__)

# # Khởi tạo logger
# logging.basicConfig(level=logging.DEBUG)

# # Giả sử bạn đã tải mô hình XGBoost đã huấn luyện
# model = xgb.Booster()
# model.load_model('room_price_xgboost_model.json')

# @app.route('/predict', methods=['POST'])
# def predict():
#     data = request.json
#     app.logger.debug('Received data: %s', data)  # Ghi log giá trị của biến data

#     # Kiểm tra xem key 'features' có tồn tại trong dữ liệu hay không
#     if 'features' not in data:
#         app.logger.error('Key "features" không tồn tại trong dữ liệu: %s', data)
#         return jsonify({'error': 'Key "features" không tồn tại trong dữ liệu'}), 400

#     try:
#         # Trích xuất các đặc trưng từ dữ liệu đầu vào
#         features_dict = {
#             'checkin_date': data['features']['checkin_date'],
#             'checkout_date': data['features']['checkout_date'],
#             'adults': data['features']['adults'],
#             'children': data['features']['children']
#         }

#         app.logger.debug('Processed features: %s', features_dict)

#         # Chuyển đổi các đặc trưng thành định dạng phù hợp cho dự đoán
#         features = np.array([
#             int(features_dict['checkin_date']),
#             int(features_dict['checkout_date']),
#             int(features_dict['adults']),
#             int(features_dict['children'])
#         ]).reshape(1, -1)

#         # Tạo DMatrix cho dự đoán
#         dmatrix = xgb.DMatrix(features, feature_names=['checkin_date', 'checkout_date', 'adults', 'children'])
#         prediction = model.predict(dmatrix)
#         app.logger.debug('Prediction: %s', prediction)

#         return jsonify({'prediction': prediction.tolist()})
#     except KeyError as e:
#         app.logger.error('Missing key in features: %s', e)
#         return jsonify({'error': f'Missing key in features: {str(e)}'}), 400
#     except ValueError as e:
#         app.logger.error('Invalid value in features: %s', e)
#         return jsonify({'error': f'Invalid value in features: {str(e)}'}), 400
#     except Exception as e:
#         app.logger.error('Error processing request: %s', e)
#         return jsonify({'error': 'Error processing request'}), 500

# if __name__ == '__main__':
#     app.run(host='0.0.0.0', port=5000)



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
            'checkin_date': data['features']['checkin_date'],
            'checkout_date': data['features']['checkout_date'],
            'adults': data['features']['adults'],
            'children': data['features']['children']
        }

        app.logger.debug('Processed features: %s', features_dict)

        # Tạo DMatrix từ từ điển đặc trưng
        features = np.array([
            int(features_dict['checkin_date']),
            int(features_dict['checkout_date']),
            int(features_dict['adults']),
            int(features_dict['children'])
        ]).reshape(1, -1)

        # In các tên đặc trưng và các giá trị để debug
        app.logger.debug('Feature names: %s', ['checkin_date', 'checkout_date', 'adults', 'children'])
        app.logger.debug('Feature values: %s', features)

        # Sử dụng từ điển đặc trưng thay vì mảng numpy
        dmatrix = xgb.DMatrix(features, feature_names=['checkin_date', 'checkout_date', 'adults', 'children'])
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
