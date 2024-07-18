from fastapi import FastAPI
from vosk import Model, KaldiRecognizer
import wave
import json
import os
from pydantic import BaseModel

app = FastAPI()
model = Model(model_path='./model')

class FileRequest(BaseModel):
    filepath: str

@app.post("/transcribe/")
async def transcribe(request: FileRequest):
    if not os.path.exists(request.filepath):
        return {"error": "File not found"}

    text = recognize_file(request.filepath)
    return {'text': text}

def recognize_file(file_path):
    with wave.open(file_path, "rb") as wf:
        rec = KaldiRecognizer(model, wf.getframerate())

        full_text = []
        while True:
            data = wf.readframes(4000)  # Читаем небольшими порциями
            if len(data) == 0:
                break
            if rec.AcceptWaveform(data):
                result = json.loads(rec.Result())
                full_text.append(result.get("text", ""))

        final_result = json.loads(rec.FinalResult())
        full_text.append(final_result.get("text", ""))

    return " ".join(full_text).strip()
