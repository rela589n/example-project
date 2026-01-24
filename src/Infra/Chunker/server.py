from functools import lru_cache
from fastapi import FastAPI
from pydantic import BaseModel
from chonkie import NeuralChunker

app = FastAPI(title="Chonkie Chunking Service")


class ChunkRequest(BaseModel):
    content: str
    model: str # "mirth/chonky_modernbert_base_1"
    min_characters_per_chunk: int

class ChunkResponse(BaseModel):
    chunks: list[Chunk]

class Chunk(BaseModel):
    index: int
    content: str
    token_count: int

@lru_cache(maxsize=2)
def get_chunker(model: str, min_characters_per_chunk: int) -> NeuralChunker:
    # See https://docs.chonkie.ai/oss/chunkers/neural-chunker
    return NeuralChunker(
        model=model,
        min_characters_per_chunk=min_characters_per_chunk
    )

@app.post("/chunk/neural/", response_model=ChunkResponse)
def chunk_text(request: ChunkRequest):
    chunker = get_chunker(request.model, request.min_characters_per_chunk)

    raw_chunks = chunker(request.content)

    return ChunkResponse(chunks=[
        Chunk(index=i, content=c.text, token_count=c.token_count)
        for i, c in enumerate(raw_chunks)
    ])


@app.get("/health")
def health():
    return {"status": "ok"}
