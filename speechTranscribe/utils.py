from pathlib import Path


def validate_file(filepath: str):
    print(filepath)
    try:
        if filepath is None:
            return {'valid': False, 'error': 'filepath argumant is empty'}

        filepath = Path(filepath)
        if not filepath.exists():
            return {'valid': False, 'error': 'file does not exists'}
    except Exception as e:
        return {'valid': False, 'error': 'unexpected error during validation'}

    return {'valid': True, 'error': ''}