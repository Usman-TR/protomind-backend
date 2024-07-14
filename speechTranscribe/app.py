from fastapi import FastAPI
from schemas import File
from utils import validate_file
from transcribe import recognize

app = FastAPI()

@app.post("/transcribe/")
async def transcribe(request: File):
    file = request.filepath or None
    validation = validate_file(file)
    if not validation['valid']:
        return validation

    text = recognize(file)
    return {'text': text}
