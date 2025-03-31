import hashlib  # Modul zur Berechnung von Hash-Werten
import time     # Modul zur Arbeit mit Zeitstempeln

# Definition einer Klasse für einen Block in der Blockchain
class Block:
    def __init__(self, index, previous_hash, data, timestamp=None):
        """
        Konstruktor für einen Block
        :param index: Position des Blocks in der Blockchain
        :param previous_hash: Hash des vorherigen Blocks
        :param data: Daten, die im Block gespeichert werden
        :param timestamp: Zeitpunkt der Erstellung des Blocks (optional)
        """
        self.index = index
        self.previous_hash = previous_hash
        self.data = data
        self.timestamp = timestamp or time.time()  # Aktueller Zeitstempel, falls nicht angegeben
        self.hash = self.calculate_hash()         # Berechnung des Hash-Werts für diesen Block

    def calculate_hash(self):
        """
        Berechnet den Hash-Wert für den Block basierend auf seinen Attributen
        :return: Hash-Wert als Hexadezimal-String
        """
        block_string = f"{self.index}{self.previous_hash}{self.data}{self.timestamp}"
        return hashlib.sha256(block_string.encode()).hexdigest()  # SHA-256-Hash des Strings


# Definition der Blockchain-Klasse
class Blockchain:
    def __init__(self):
        """
        Konstruktor für die Blockchain. Initialisiert die Kette mit einem Genesis-Block.
        """
        self.chain = [self.create_genesis_block()]  # Liste von Blöcken, beginnend mit dem Genesis-Block

    def create_genesis_block(self):
        """
        Erstellt den Genesis-Block (erster Block in der Blockchain)
        :return: Genesis-Block
        """
        return Block(0, "0", "Genesis Block")  # Index 0, vorheriger Hash "0", Daten "Genesis Block"

    def add_block(self, data):
        """
        Fügt der Blockchain einen neuen Block hinzu.
        :param data: Daten, die im neuen Block gespeichert werden sollen
        """
        last_block = self.chain[-1]  # Letzter Block in der Blockchain
        new_block = Block(len(self.chain), last_block.hash, data)  # Neuer Block mit Verweis auf den letzten Block
        self.chain.append(new_block)  # Hinzufügen des neuen Blocks zur Blockchain

    def is_chain_valid(self):
        """
        Überprüft die Integrität der Blockchain.
        :return: True, wenn die Blockchain gültig ist, False ansonsten
        """
        for i in range(1, len(self.chain)):
            current = self.chain[i]
            previous = self.chain[i - 1]
            # Prüft, ob der aktuelle Hash korrekt ist
            if current.hash != current.calculate_hash():
                return False
            # Prüft, ob der vorherige Hash mit dem Hash des vorherigen Blocks übereinstimmt
            if current.previous_hash != previous.hash:
                return False
        return True  # Blockchain ist gültig, wenn alle Prüfungen bestanden sind


# Hauptprogramm zum Testen der Blockchain
if __name__ == "__main__":
    # Eine neue Blockchain erstellen
    my_blockchain = Blockchain()
    
    # Neue Blöcke mit Daten hinzufügen
    my_blockchain.add_block("Erster Beitrag")  # Block 1
    my_blockchain.add_block("Zweiter Beitrag")  # Block 2
    my_blockchain.add_block("Dritter Beitrag")  # Block 3
    
    # Die gesamte Blockchain ausgeben
    for block in my_blockchain.chain:
        print(f"Index: {block.index}")
        print(f"Timestamp: {block.timestamp}")
        print(f"Data: {block.data}")
        print(f"Previous Hash: {block.previous_hash}")
        print(f"Hash: {block.hash}")
        print("-" * 30)
    
    # Prüfen, ob die Blockchain gültig ist
    print("Ist die Blockchain gültig?", my_blockchain.is_chain_valid())
