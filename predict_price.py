# import sys
# import json
# from xgboost import Booster

# def predict_price(room_id, checkin_date, checkout_date, adults, children):
#     # Load XGBoost model
#     booster = Booster()
#     booster.load_model('room_price_xgboost_model.json')

#     # Prepare input data
#     data = {
#         'room_id': room_id,
#         'checkin_date': checkin_date,
#         'checkout_date': checkout_date,
#         'adults': adults,
#         'children': children
#     }

#     # Perform prediction
#     predicted_price = booster.predict(data)

#     return predicted_price

# # Read arguments from command line
# room_id, checkin_date, checkout_date, adults, children = sys.argv[1:]

# # Call predict_price function
# result = predict_price(room_id, checkin_date, checkout_date, adults, children)

# # Print predicted price
# print(result)


import xgboost as xgb
import pandas as pd

# Assuming you have your data in a pandas DataFrame
data = pd.DataFrame({
    'room_id': [123],
    'checkin_date': ['2024-05-09'],
    'checkout_date': ['2024-05-11'],
    'adults': [2],
    'children': [1]
})

# Convert the DataFrame to a DMatrix
dmatrix = xgb.DMatrix(data)

# Load your trained booster model (replace 'path_to_model' with the actual path)
booster = xgb.Booster(model_file='room_price_xgboost_model.json')

# Make predictions
predicted_price = booster.predict(dmatrix)

print("Predicted price:", predicted_price)
