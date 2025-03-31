from flask import Flask, jsonify, request  # Flask für die Web-App und JSON-Handling.
from flask_cors import CORS  # Ermöglicht Cross-Origin-Resource-Sharing.
import time  # Für Zeitstempel von Blöcken.
import hashlib  # Für die Erstellung von Hashes.

# Flask-App initialisieren.
app = Flask(__name__)

# CORS für alle Routen aktivieren.
CORS(app, resources={r"/*": {"origins": "*"}})

# Middleware, um CORS-Header zu allen Antworten hinzuzufügen.
@app.after_request
def add_cors_headers(response):
    # Header für CORS-Konfiguration.
    response.headers["Access-Control-Allow-Origin"] = "*"
    response.headers["Access-Control-Allow-Methods"] = "GET, POST, OPTIONS"
    response.headers["Access-Control-Allow-Headers"] = "Content-Type, Authorization"
    return response

# Definition der Block-Klasse.
class Block:
    def __init__(self, index, previous_hash, data, timestamp=None):
        self.index = index  # Index des Blocks in der Blockchain.
        self.previous_hash = previous_hash  # Hash des vorherigen Blocks.
        self.data = data  # Daten, die im Block gespeichert sind.
        self.timestamp = timestamp or time.time()  # Zeitstempel des Blocks.
        self.hash = self.calculate_hash()  # Hash-Wert des aktuellen Blocks.

    def calculate_hash(self):
        # Berechnung des Hash-Werts basierend auf Blockdaten.
        block_string = f"{self.index}{self.previous_hash}{self.data}{self.timestamp}"
        return hashlib.sha256(block_string.encode()).hexdigest()

# Definition der Blockchain-Klasse.
class Blockchain:
    def __init__(self):
        # Initialisierung der Blockchain mit einem Genesis-Block.
        self.chain = [self.create_genesis_block()]

    def create_genesis_block(self):
        # Erzeugung des ersten Blocks (Genesis-Block).
        return Block(0, "0", "Genesis Block")

    def add_block(self, data):
        # Hinzufügen eines neuen Blocks zur Blockchain.
        last_block = self.chain[-1]  # Letzter Block in der Kette.
        new_block = Block(len(self.chain), last_block.hash, data)  # Neuer Block.
        self.chain.append(new_block)  # Block zur Kette hinzufügen.

    def is_chain_valid(self):
        # Validierung der gesamten Blockchain.
        for i in range(1, len(self.chain)):
            current = self.chain[i]
            previous = self.chain[i - 1]
            # Überprüfung des aktuellen Hashes.
            if current.hash != current.calculate_hash():
                return False
            # Überprüfung der Verknüpfung zum vorherigen Block.
            if current.previous_hash != previous.hash:
                return False
        return True

# Initialisierung der Blockchain-Instanz.
blockchain = Blockchain()

# Route für die Startseite der API.
@app.route('/')
def home():
    return (
        "<h1>Willkommen bei der Blockchain API</h1>"
        "<p>Verfügbare Endpunkte:</p>"
        "<ul>"
        "<li><a href='/blocks'>/blocks</a></li>"
        "<li><a href='/add_block'>/add_block</a></li>"
        "<li><a href='/is_valid'>/is_valid</a></li>"
        "</ul>"
    )

# Route für Favicon-Anfragen, ohne Inhalt.
@app.route('/favicon.ico')
def favicon():
    return '', 204

# Route zum Abrufen aller Blöcke in der Blockchain.
@app.route('/blocks', methods=['GET'])
def get_blocks():
    chain_data = []  # Liste für die Blockdaten.
    for block in blockchain.chain:
        # Blockdaten in ein Wörterbuch umwandeln.
        chain_data.append({
            "index": block.index,
            "timestamp": block.timestamp,
            "data": block.data,
            "previous_hash": block.previous_hash,
            "hash": block.hash
        })
    return jsonify(chain_data)  # Daten als JSON zurückgeben.

# Route zum Hinzufügen eines neuen Blocks.
@app.route('/add_block', methods=['POST'])
def add_block():
    data = request.json.get("data", "")  # Daten aus der Anfrage extrahieren.
    blockchain.add_block(data)  # Block mit den Daten hinzufügen.
    return jsonify({"message": "Block added!"}), 201  # Bestätigung zurückgeben.

# Route zur Überprüfung der Blockchain-Gültigkeit.
@app.route('/is_valid', methods=['GET'])
def is_valid():
    return jsonify({"is_valid": blockchain.is_chain_valid()})  # Ergebnis zurückgeben.

# Route für OPTIONS-Anfragen (z. B. für Preflight).
@app.route('/<path:path>', methods=['OPTIONS'])
def options(path):
    return '', 204

# Start des Servers.
if __name__ == "__main__":
    # App läuft auf Host 0.0.0.0 mit Port 5000 und SSL-Verschlüsselung.
    app.run(host="0.0.0.0", port=5000, ssl_context=('cert.pem', 'key.pem'))
