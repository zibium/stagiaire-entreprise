#!/usr/bin/env python3
# Serveur web simple pour contourner les problèmes PHP de Laragon

import http.server
import socketserver
import os
import subprocess
import sys
from urllib.parse import urlparse, parse_qs

class PHPHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        self.handle_request()
    
    def do_POST(self):
        self.handle_request()
    
    def handle_request(self):
        # Parse l'URL
        parsed_path = urlparse(self.path)
        path = parsed_path.path
        
        # Si c'est la racine, rediriger vers index.php
        if path == '/' or path == '':
            path = '/index.php'
        
        # Construire le chemin du fichier
        file_path = os.path.join('public', path.lstrip('/'))
        
        # Si c'est un fichier PHP
        if file_path.endswith('.php') and os.path.exists(file_path):
            try:
                # Exécuter PHP avec le fichier
                result = subprocess.run(
                    ['php', file_path],
                    capture_output=True,
                    text=True,
                    cwd=os.getcwd()
                )
                
                # Envoyer la réponse
                self.send_response(200)
                self.send_header('Content-type', 'text/html; charset=utf-8')
                self.end_headers()
                
                if result.returncode == 0:
                    self.wfile.write(result.stdout.encode('utf-8'))
                else:
                    error_msg = f"<h1>Erreur PHP</h1><pre>{result.stderr}</pre>"
                    self.wfile.write(error_msg.encode('utf-8'))
                    
            except Exception as e:
                self.send_response(500)
                self.send_header('Content-type', 'text/html; charset=utf-8')
                self.end_headers()
                error_msg = f"<h1>Erreur serveur</h1><pre>{str(e)}</pre>"
                self.wfile.write(error_msg.encode('utf-8'))
        
        # Si c'est un fichier statique
        elif os.path.exists(file_path) and os.path.isfile(file_path):
            super().do_GET()
        
        # Sinon, 404
        else:
            self.send_response(404)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(b"<h1>404 - Page non trouvee</h1>")

if __name__ == "__main__":
    PORT = 8000
    
    # Changer vers le répertoire du projet
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    with socketserver.TCPServer(("", PORT), PHPHandler) as httpd:
        print(f"Serveur démarré sur http://localhost:{PORT}")
        print("Appuyez sur Ctrl+C pour arrêter")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\nServeur arrêté")
            httpd.shutdown()