from pyzbar.pyzbar import decode
from PIL import Image

# Abre a imagem
imagem = Image.open("qrcode.jpg")

# Decodifica QR codes
decodificados = decode(imagem)

for qr in decodificados:
    print("Conteúdo:", qr.data.decode("utf-8"))
