import wave
import json
from vosk import Model, KaldiRecognizer

model = Model(model_path='./model')

def recognize(fn):
    wf = wave.open(fn, "rb")
    rec = KaldiRecognizer(model, wf.getframerate())

    text = ""
    while True:
        data = wf.readframes(1000)
        if len(data) == 0:
            break
        if rec.AcceptWaveform(data):
            jres = json.loads(rec.Result())
            text = text + " " + jres["text"]
    jres = json.loads(rec.FinalResult())
    text = text + " " + jres["text"]
    return text