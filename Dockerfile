# Gunakan Python image resmi
FROM python:3.10

# Set working directory
WORKDIR /app

# Copy semua file ke container
COPY . /app

# Install dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Jalankan aplikasi
CMD ["python", "app.py"]
