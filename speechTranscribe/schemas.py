from pydantic import BaseModel

class File(BaseModel):
    filepath: str