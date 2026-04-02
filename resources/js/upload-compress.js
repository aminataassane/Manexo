/**
 * Réduit la taille des images avant l’upload Livewire (photos mobile, captures).
 * Les PDF / bureautique passent inchangés.
 */
const MAX_DIMENSION = 2048;
const MIN_SIZE_TO_COMPRESS = 350 * 1024;
const JPEG_QUALITY = 0.82;

function shouldTryCompress(file) {
    if (!file.type.startsWith('image/')) {
        return false;
    }
    if (file.type === 'image/gif') {
        return false;
    }
    if (file.size < MIN_SIZE_TO_COMPRESS) {
        return false;
    }
    return true;
}

async function compressImageFile(file) {
    let bitmap;
    try {
        bitmap = await createImageBitmap(file);
    } catch {
        return file;
    }

    let { width, height } = bitmap;
    const scale = Math.min(1, MAX_DIMENSION / Math.max(width, height));
    width = Math.round(width * scale);
    height = Math.round(height * scale);

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        bitmap.close?.();
        return file;
    }
    ctx.drawImage(bitmap, 0, 0, width, height);
    bitmap.close?.();

    const outType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
    const quality = outType === 'image/png' ? undefined : JPEG_QUALITY;

    const blob = await new Promise((resolve) => {
        canvas.toBlob((b) => resolve(b), outType, quality);
    });

    if (!blob || blob.size >= file.size * 0.95) {
        return file;
    }

    const name =
        outType === 'image/jpeg' && !/\.jpe?g$/i.test(file.name)
            ? file.name.replace(/\.[^.]+$/, '.jpg')
            : file.name;

    return new File([blob], name, { type: outType, lastModified: Date.now() });
}

export async function manexoCompressFilesForUpload(fileList) {
    const files = Array.from(fileList || []);
    const out = [];
    for (const file of files) {
        if (!shouldTryCompress(file)) {
            out.push(file);
            continue;
        }
        try {
            const compressed = await compressImageFile(file);
            out.push(compressed);
        } catch {
            out.push(file);
        }
    }
    return out;
}
