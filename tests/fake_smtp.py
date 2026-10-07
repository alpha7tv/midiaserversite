#!/usr/bin/env python3
"""Servidor SMTP falso (sem TLS) para testar o Mailer. Grava mensagens recebidas em /tmp/fake_smtp_inbox.jsonl"""
import socketserver, json, base64, sys
PORT = int(sys.argv[1]) if len(sys.argv) > 1 else 2525
class H(socketserver.StreamRequestHandler):
    def send(self, s): self.wfile.write((s + "\r\n").encode())
    def handle(self):
        self.send("220 fake ESMTP"); msg = {"auth": [], "rcpt": []}
        while True:
            line = self.rfile.readline().decode(errors='replace').rstrip("\r\n")
            if not line: break
            u = line.upper()
            if u.startswith("EHLO"): self.send("250-fake"); self.send("250 AUTH LOGIN")
            elif u == "AUTH LOGIN": self.send("334 VXNlcm5hbWU6"); msg["auth"].append(base64.b64decode(self.rfile.readline().strip()).decode()); self.send("334 UGFzc3dvcmQ6"); msg["auth"].append(base64.b64decode(self.rfile.readline().strip()).decode()); self.send("235 ok")
            elif u.startswith("MAIL FROM"): msg["from"] = line[10:]; self.send("250 ok")
            elif u.startswith("RCPT TO"): msg["rcpt"].append(line[8:]); self.send("250 ok")
            elif u == "DATA":
                self.send("354 go"); data = []
                while True:
                    l = self.rfile.readline().decode(errors='replace').rstrip("\r\n")
                    if l == ".": break
                    data.append(l[1:] if l.startswith("..") else l)
                msg["data"] = "\n".join(data); self.send("250 queued")
                open("/tmp/fake_smtp_inbox.jsonl", "a").write(json.dumps(msg) + "\n"); msg = {"auth": msg["auth"], "rcpt": []}
            elif u == "QUIT": self.send("221 bye"); break
            else: self.send("250 ok")
socketserver.ThreadingTCPServer.allow_reuse_address = True
socketserver.ThreadingTCPServer(("127.0.0.1", PORT), H).serve_forever()
