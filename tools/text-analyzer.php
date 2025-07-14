<?php
// tools/text-analyzer.php
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Metin analizi fonksiyonu
function analyzeText($text) {
    if (empty($text) || trim($text) === '') return null;
    
    // Temel sayımlar
    $characters = mb_strlen($text, 'UTF-8');
    $charactersNoSpaces = mb_strlen(str_replace(' ', '', $text), 'UTF-8');
    $words = str_word_count($text);
    $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $sentenceCount = count($sentences);
    $paragraphs = preg_split('/\n\s*\n/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    $paragraphCount = count($paragraphs);
    
    // Ortalama hesaplamalar
    $avgWordsPerSentence = $sentenceCount > 0 ? round($words / $sentenceCount, 1) : 0;
    $avgCharsPerWord = $words > 0 ? round($charactersNoSpaces / $words, 1) : 0;
    
    // En uzun kelime
    $wordArray = str_word_count($text, 1);
    $longestWord = '';
    if (!empty($wordArray)) {
        $longestWord = array_reduce($wordArray, function($a, $b) {
            return mb_strlen($a, 'UTF-8') > mb_strlen($b, 'UTF-8') ? $a : $b;
        });
    }
    
    // Okuma süresi (dakika) - ortalama 200 kelime/dakika
    $readingTime = $words > 0 ? ceil($words / 200) : 0;
    
    // En sık kullanılan kelimeler
    $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text, 'UTF-8'));
    $wordFreq = array_count_values(str_word_count($cleanText, 1));
    arsort($wordFreq);
    $topWords = array_slice($wordFreq, 0, 5, true);
    
    return [
        'characters' => $characters,
        'characters_no_spaces' => $charactersNoSpaces,
        'words' => $words,
        'sentences' => $sentenceCount,
        'paragraphs' => $paragraphCount,
        'avg_words_per_sentence' => $avgWordsPerSentence,
        'avg_chars_per_word' => $avgCharsPerWord,
        'longest_word' => $longestWord,
        'reading_time' => $readingTime,
        'top_words' => $topWords
    ];
}

$result = null;
$text = $_POST['text'] ?? '';

if (!empty($text)) {
    $result = analyzeText($text);
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <h1><i class="fas fa-file-alt text-primary"></i> 
            <?php echo ($currentLang === 'tr') ? 'Metin Analizi' : 'Text Analyzer'; ?>
        </h1>
        <p class="lead">
            <?php echo ($currentLang === 'tr') ? 'Metninizin kelime, karakter sayısını analiz edin' : 'Analyze word and character count of your text'; ?>
        </p>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="textForm">
                            <div class="mb-3">
                                <label class="form-label">
                                    <?php echo ($currentLang === 'tr') ? 'Analiz Edilecek Metin' : 'Text to Analyze'; ?>
                                </label>
                                <textarea name="text" class="form-control" rows="10" 
                                          placeholder="<?php echo ($currentLang === 'tr') ? 'Metninizi buraya yazın veya yapıştırın...' : 'Type or paste your text here...'; ?>"
                                          oninput="updateLiveStats()"><?php echo htmlspecialchars($text); ?></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Analiz Et' : 'Analyze'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="clearText()">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>

                        <!-- Canlı sayaç -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6><?php echo ($currentLang === 'tr') ? 'Canlı Sayaç' : 'Live Counter'; ?></h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <strong id="liveChars">0</strong><br>
                                            <small><?php echo ($currentLang === 'tr') ? 'Karakter' : 'Characters'; ?></small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <strong id="liveWords">0</strong><br>
                                            <small><?php echo ($currentLang === 'tr') ? 'Kelime' : 'Words'; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?php if ($result): ?>
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <?php echo ($currentLang === 'tr') ? 'Analiz Sonucu' : 'Analysis Result'; ?>
                        </h5>
                        
                        <!-- Temel sayımlar -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-primary text-white rounded">
                                    <strong><?php echo number_format($result['characters']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Karakter' : 'Characters'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-info text-white rounded">
                                    <strong><?php echo number_format($result['words']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Kelime' : 'Words'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-success text-white rounded">
                                    <strong><?php echo number_format($result['sentences']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Cümle' : 'Sentences'; ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-warning text-white rounded">
                                    <strong><?php echo number_format($result['paragraphs']); ?></strong><br>
                                    <small><?php echo ($currentLang === 'tr') ? 'Paragraf' : 'Paragraphs'; ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Detaylı bilgiler -->
                        <div class="card">
                            <div class="card-body">
                                <h6><?php echo ($currentLang === 'tr') ? 'Detaylar' : 'Details'; ?></h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><?php echo ($currentLang === 'tr') ? 'Boşluksuz karakter:' : 'Characters (no spaces):'; ?></td>
                                        <td><strong><?php echo number_format($result['characters_no_spaces']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo ($currentLang === 'tr') ? 'Ortalama kelime/cümle:' : 'Avg words/sentence:'; ?></td>
                                        <td><strong><?php echo $result['avg_words_per_sentence']; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo ($currentLang === 'tr') ? 'Ortalama harf/kelime:' : 'Avg chars/word:'; ?></td>
                                        <td><strong><?php echo $result['avg_chars_per_word']; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo ($currentLang === 'tr') ? 'En uzun kelime:' : 'Longest word:'; ?></td>
                                        <td><strong><?php echo htmlspecialchars($result['longest_word']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo ($currentLang === 'tr') ? 'Okuma süresi:' : 'Reading time:'; ?></td>
                                        <td><strong><?php echo $result['reading_time']; ?> 
                                            <?php echo ($currentLang === 'tr') ? 'dakika' : 'minutes'; ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- En sık kullanılan kelimeler -->
                        <?php if (!empty($result['top_words'])): ?>
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6><?php echo ($currentLang === 'tr') ? 'En Sık Kullanılan Kelimeler' : 'Most Frequent Words'; ?></h6>
                                <div class="row">
                                    <?php foreach ($result['top_words'] as $word => $count): ?>
                                        <div class="col-12 mb-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><?php echo htmlspecialchars($word); ?></span>
                                                <span class="badge bg-secondary"><?php echo $count; ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <h6><?php echo ($currentLang === 'tr') ? 'Metin Analizi Hakkında' : 'About Text Analysis'; ?></h6>
                        <ul class="small">
                            <li><?php echo ($currentLang === 'tr') ? 'Karakter ve kelime sayısı' : 'Character and word count'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Cümle ve paragraf analizi' : 'Sentence and paragraph analysis'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Okuma süresi tahmini' : 'Reading time estimation'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Kelime sıklığı analizi' : 'Word frequency analysis'; ?></li>
                            <li><?php echo ($currentLang === 'tr') ? 'Canlı sayaç desteği' : 'Live counter support'; ?></li>
                        </ul>

                        <div class="alert alert-info mt-3">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <?php echo ($currentLang === 'tr') ? 
                                    'Metninizi yazarken canlı sayaç otomatik güncellenir.' : 
                                    'Live counter updates automatically as you type.'; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateLiveStats() {
    const text = document.querySelector('textarea[name="text"]').value;
    const chars = text.length;
    const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
    
    document.getElementById('liveChars').textContent = chars.toLocaleString();
    document.getElementById('liveWords').textContent = words.toLocaleString();
}

function clearText() {
    document.querySelector('textarea[name="text"]').value = '';
    updateLiveStats();
}

// Sample texts for quick testing
const sampleTexts = {
    tr: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.

Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.`,
    
    en: `The quick brown fox jumps over the lazy dog. This sentence contains every letter of the alphabet at least once. It is commonly used for testing fonts and keyboards.

Text analysis is important for understanding content structure. Writers and editors use these tools to improve their work and ensure readability.`
};

// Add sample text button
document.addEventListener('DOMContentLoaded', function() {
    const lang = '<?php echo $currentLang; ?>';
    const textarea = document.querySelector('textarea[name="text"]');
    
    // Create sample text button
    const sampleBtn = document.createElement('button');
    sampleBtn.type = 'button';
    sampleBtn.className = 'btn btn-outline-info btn-sm mt-2';
    sampleBtn.innerHTML = '<i class="fas fa-file-text"></i> ' + (lang === 'tr' ? 'Örnek Metin Yükle' : 'Load Sample Text');
    sampleBtn.onclick = function() {
        textarea.value = sampleTexts[lang] || sampleTexts.en;
        updateLiveStats();
    };
    
    textarea.parentNode.appendChild(sampleBtn);
    
    // Initialize live stats
    updateLiveStats();
});
</script>
</body>
</html>