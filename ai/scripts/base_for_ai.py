import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# Step 1: Load historical data and train a prediction model
def train_model(historical_data_file):
    # Load historical data
    data = pd.read_csv(historical_data_file)
    
    # Assume the last column is the flood risk (target variable), and the rest are features
    X = data.iloc[:, :-1]  # Features
    y = data.iloc[:, -1]   # Target variable (flood risk)

    # Split the data into training and testing sets
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # Train a Random Forest model
    model = RandomForestClassifier()
    model.fit(X_train, y_train)

    # Evaluate the model
    y_pred = model.predict(X_test)
    print(f"Model accuracy: {accuracy_score(y_test, y_pred)}")
    
    return model

# Step 2: Make a prediction based on new real-time data (NRT data)
def make_prediction(model, nrt_data_file):
    # Load new real-time data
    nrt_data = pd.read_csv(nrt_data_file)

    # Assuming the structure is the same as historical data (same features)
    features = nrt_data.iloc[:, :-1]  # Features (excluding target)
    
    # Make a prediction for the flood risk level
    flood_risk_prediction = model.predict(features)
    return flood_risk_prediction

# Step 3: Monitor flood risk and send notifications
def monitor_flood_risk(flood_risk_prediction):
    # Simple logic to send a notification based on flood risk prediction
    for risk_level in flood_risk_prediction:
        if risk_level == "High":
            send_notification("Flood warning: High risk, evacuation advised!")
        elif risk_level == "Medium":
            send_notification("Flood warning: Medium risk, prepare for possible flooding.")
        elif risk_level == "Low":
            send_notification("Flood risk: Low, but stay alert.")

# Step 4: Send an email notification
def send_notification(message):
    # Set up the email details
    sender_email = "your_email@example.com"
    receiver_email = "receiver_email@example.com"
    password = "your_email_password"  # Use app-specific password for Gmail or other providers
    
    # Create email content
    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = receiver_email
    msg['Subject'] = "Flood Alert Notification"
    msg.attach(MIMEText(message, 'plain'))
    
    # Send the email
    try:
        server = smtplib.SMTP('smtp.gmail.com', 587)  # Using Gmail SMTP server for this example
        server.starttls()  # Secure the connection
        server.login(sender_email, password)
        text = msg.as_string()
        server.sendmail(sender_email, receiver_email, text)
        server.quit()
        print(f"Notification sent: {message}")
    except Exception as e:
        print(f"Failed to send notification: {str(e)}")

# Step 5: Main function to run everything
def run():
    # Train the model on historical data
    historical_data_file = "historical_data.csv"
    model = train_model(historical_data_file)
    
    # Predict flood risk with new real-time data
    nrt_data_file = "nrt_data.csv"
    flood_risk_prediction = make_prediction(model, nrt_data_file)
    
    # Monitor and send notifications
    monitor_flood_risk(flood_risk_prediction)

if __name__ == "__main__":
    run()
