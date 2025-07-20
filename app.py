import streamlit as st
import numpy as np
import tensorflow as tf
import pickle
from tensorflow.keras.preprocessing.sequence import pad_sequences
import re

# Load model
model = tf.keras.models.load_model("lstm_emosi_puisi.h5")

# Load tokenizer
with open("tokenizer.pkl", "rb") as f:
    tokenizer = pickle.load(f)

# Load label encoder
with open("label_encoder.pkl", "rb") as f:
    label_encoder = pickle.load(f)

MAX_LEN = 100

def clean_text(text):
    text = text.lower()
    text = re.sub(r'\n', ' ', text)
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    text = re.sub(r'\s+', ' ', text).strip()
    return text

st.title("🎭 Deteksi Emosi Puisi")

text_input = st.text_area("Masukkan puisi di sini:")

if st.button("Prediksi Emosi"):
    if text_input:
        cleaned = clean_text(text_input)
        sequence = tokenizer.texts_to_sequences([cleaned])
        padded = pad_sequences(sequence, maxlen=MAX_LEN, padding='post')

        prediction = model.predict(padded)
        index = np.argmax(prediction)
        label = label_encoder.inverse_transform([index])[0]
        confidence = float(np.max(prediction)) * 100

        st.success(f"Emosi: {label} ({confidence:.2f}%)")
    else:
        st.warning("Teks puisi tidak boleh kosong.")
