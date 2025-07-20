from flask import Flask, request, jsonify
import numpy as np
import tensorflow as tf
from tensorflow.keras.preprocessing.sequence import pad_sequences
import pickle
import re

app = Flask(__name__)

# Load model
model = tf.keras.models.load_model("lstm_emosi_puisi.h5")

# Load tokenizer
with open("tokenizer.pkl", "rb") as f:
    tokenizer = pickle.load(f)

# Load label encoder
with open("label_encoder.pkl", "rb") as f:
    label_encoder = pickle.load(f)

MAX_LEN = 100  # Sesuai pelatihan model

def clean_text(text):
    text = text.lower()
    text = re.sub(r'\n', ' ', text)
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    text = re.sub(r'\s+', ' ', text).strip()
    return text

@app.route("/predict", methods=["POST"])
def predict():
    try:
        data = request.get_json()

        if not data or "text" not in data:
            return jsonify({"error": "❌ Harap sertakan field 'text' dalam JSON."}), 400

        input_text = clean_text(data["text"])
        sequence = tokenizer.texts_to_sequences([input_text])
        padded = pad_sequences(sequence, maxlen=MAX_LEN, padding='post')

        prediction = model.predict(padded)
        predicted_index = np.argmax(prediction)
        predicted_label = label_encoder.inverse_transform([predicted_index])[0]
        confidence = float(np.max(prediction)) * 100

        return jsonify({
            "input_text": data["text"],
            "predicted_emotion": predicted_label,
            "confidence": f"{confidence:.2f}%"
        })

    except Exception as e:
        return jsonify({"error": f"❌ Terjadi kesalahan pada server: {str(e)}"}), 500

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5050, debug=True)
