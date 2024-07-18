from fastapi import FastAPI
from vosk import Model, KaldiRecognizer
import wave
import json
import os
from pydantic import BaseModel
from concurrent.futures import ThreadPoolExecutor, as_completed

app = FastAPI()
model = Model(model_path='./model')

class FileRequest(BaseModel):
    filepath: str

@app.post("/transcribe/")
async def transcribe(request: FileRequest):
    if not os.path.exists(request.filepath):
        return {"error": "File not found"}

    text = recognize_large_file(request.filepath)
    return {'text': text}

def process_chunk(chunk, sample_rate):
    rec = KaldiRecognizer(model, sample_rate)
    if rec.AcceptWaveform(chunk):
        result = json.loads(rec.Result())
        return result.get("text", "")
    return ""

def recognize_large_file(file_path, num_threads=4):
    with wave.open(file_path, "rb") as wf:
        sample_rate = wf.getframerate()
        total_samples = wf.getnframes()
        chunk_duration = 10  # seconds
        chunk_size = int(sample_rate * chunk_duration)

        chunks = []
        for i in range(0, total_samples, chunk_size):
            wf.setpos(i)
            chunk = wf.readframes(chunk_size)
            chunks.append(chunk)

    full_text = ""
    with ThreadPoolExecutor(max_workers=num_threads) as executor:
        future_to_chunk = {executor.submit(process_chunk, chunk, sample_rate): chunk for chunk in chunks}
        for future in as_completed(future_to_chunk):
            full_text += future.result() + " "

    rec = KaldiRecognizer(model, sample_rate)
    final_result = json.loads(rec.FinalResult())
    full_text += final_result.get("text", "")

    return full_text.strip()
