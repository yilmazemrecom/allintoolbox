<?php
// tools/text-analyzer.php - TAM VERSİYON
session_start();

require_once '../config/config.php';
require_once '../config/functions.php';

$currentLang = $_GET['lang'] ?? detectLanguage();
setLanguage($currentLang);

// Sayfa meta bilgileri
$pageTitle = ($currentLang === 'tr') ? 'Metin Analizi' : 'Text Analyzer';
$pageDescription = ($currentLang === 'tr') ? 
    'Metin analizi aracı. Kelime, karakter sayısı, okuma süresi ve kelime sıklığı analizi.' :
    'Text analysis tool. Word count, character count, reading time and word frequency analysis.';

// Gelişmiş metin analizi fonksiyonu
function analyzeText($text) {
    if (empty($text) || trim($text) === '') return null;
    
    $originalText = $text;
    $text = trim($text);
    
    // Temel sayımlar
    $characters = mb_strlen($originalText, 'UTF-8');
    $charactersNoSpaces = mb_strlen(preg_replace('/\s/', '', $originalText), 'UTF-8');
    $charactersNoSpacesNoPunctuation = mb_strlen(preg_replace('/[^\p{L}\p{N}]/u', '', $originalText), 'UTF-8');
    
    // Kelime sayımı (Unicode uyumlu)
    $words = str_word_count($text);
    if ($words === 0) {
        // Fallback for non-Latin scripts
        $wordArray = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $words = count($wordArray);
    }
    
    // Cümle analizi (gelişmiş)
    $sentences = preg_split('/[.!?]+(?=\s|$)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    $sentences = array_filter($sentences, function($s) { return trim($s) !== ''; });
    $sentenceCount = count($sentences);
    
    // Paragraf analizi
    $paragraphs = preg_split('/\n\s*\n/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    $paragraphs = array_filter($paragraphs, function($p) { return trim($p) !== ''; });
    $paragraphCount = count($paragraphs);
    
    // Satır sayısı
    $lines = explode("\n", $originalText);
    $lineCount = count($lines);
    
    // Ortalama hesaplamalar
    $avgWordsPerSentence = $sentenceCount > 0 ? round($words / $sentenceCount, 1) : 0;
    $avgCharsPerWord = $words > 0 ? round($charactersNoSpaces / $words, 1) : 0;
    $avgSentencesPerParagraph = $paragraphCount > 0 ? round($sentenceCount / $paragraphCount, 1) : 0;
    
    // Kelime analizi
    $wordArray = str_word_count($text, 1);
    if (empty($wordArray)) {
        // Fallback for non-Latin scripts
        $wordArray = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    }
    
    // En uzun ve en kısa kelime
    $longestWord = '';
    $shortestWord = '';
    if (!empty($wordArray)) {
        $longestWord = array_reduce($wordArray, function($a, $b) {
            return mb_strlen($a, 'UTF-8') > mb_strlen($b, 'UTF-8') ? $a : $b;
        });
        $shortestWord = array_reduce($wordArray, function($a, $b) {
            return mb_strlen($a, 'UTF-8') < mb_strlen($b, 'UTF-8') ? $a : $b;
        });
    }
    
    // Okuma süreleri (farklı hızlar)
    $readingTimeSlow = $words > 0 ? ceil($words / 150) : 0;    // Yavaş okuma
    $readingTimeAverage = $words > 0 ? ceil($words / 200) : 0; // Ortalama okuma
    $readingTimeFast = $words > 0 ? ceil($words / 250) : 0;    // Hızlı okuma
    
    // Konuşma süresi (ortalama 130 kelime/dakika)
    $speakingTime = $words > 0 ? ceil($words / 130) : 0;
    
    // Kelime sıklığı analizi (gelişmiş)
    $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text, 'UTF-8'));
    $wordFreq = [];
    if (!empty($cleanText)) {
        $words_for_freq = str_word_count($cleanText, 1);
        if (empty($words_for_freq)) {
            $words_for_freq = preg_split('/\s+/', trim($cleanText), -1, PREG_SPLIT_NO_EMPTY);
        }
        
        // Çok kısa kelimeleri filtrele (2 karakterden az)
        $words_for_freq = array_filter($words_for_freq, function($word) {
            return mb_strlen($word, 'UTF-8') >= 2;
        });
        
        $wordFreq = array_count_values($words_for_freq);
        arsort($wordFreq);
    }
    $topWords = array_slice($wordFreq, 0, 10, true);
    
    // Karakter türü analizi
    $upperCase = preg_match_all('/\p{Lu}/u', $originalText);
    $lowerCase = preg_match_all('/\p{Ll}/u', $originalText);
    $numbers = preg_match_all('/\d/u', $originalText);
    $punctuation = preg_match_all('/[^\p{L}\p{N}\s]/u', $originalText);
    $spaces = preg_match_all('/\s/u', $originalText);
    
    // Readability skorları (basitleştirilmiş)
    $avgWordsPerSentenceFloat = $sentenceCount > 0 ? $words / $sentenceCount : 0;
    $avgSyllablesPerWord = $avgCharsPerWord * 0.4; // Yaklaşık hesaplama
    
    // Flesch Reading Ease (basitleştirilmiş)
    $fleschScore = 0;
    if ($words > 0 && $sentenceCount > 0) {
        $fleschScore = 206.835 - (1.015 * $avgWordsPerSentenceFloat) - (84.6 * $avgSyllablesPerWord);
        $fleschScore = max(0, min(100, round($fleschScore, 1)));
    }
    
    // Benzersiz kelime yüzdesi
    $uniqueWordPercentage = count($wordFreq) > 0 && $words > 0 ? round((count($wordFreq) / $words) * 100, 1) : 0;
    
    return [
        'characters' => $characters,
        'characters_no_spaces' => $charactersNoSpaces,
        'characters_no_spaces_no_punctuation' => $charactersNoSpacesNoPunctuation,
        'words' => $words,
        'sentences' => $sentenceCount,
        'paragraphs' => $paragraphCount,
        'lines' => $lineCount,
        'avg_words_per_sentence' => $avgWordsPerSentence,
        'avg_chars_per_word' => $avgCharsPerWord,
        'avg_sentences_per_paragraph' => $avgSentencesPerParagraph,
        'longest_word' => $longestWord,
        'shortest_word' => $shortestWord,
        'reading_time_slow' => $readingTimeSlow,
        'reading_time_average' => $readingTimeAverage,
        'reading_time_fast' => $readingTimeFast,
        'speaking_time' => $speakingTime,
        'top_words' => $topWords,
        'unique_words' => count($wordFreq),
        'unique_word_percentage' => $uniqueWordPercentage,
        'character_breakdown' => [
            'uppercase' => $upperCase,
            'lowercase' => $lowerCase,
            'numbers' => $numbers,
            'punctuation' => $punctuation,
            'spaces' => $spaces
        ],
        'flesch_score' => $fleschScore,
        'most_common_word' => !empty($topWords) ? array_key_first($topWords) : '',
        'most_common_word_count' => !empty($topWords) ? reset($topWords) : 0
    ];
}

// Flesch Reading Ease skorunu yorumlama
function getFleschInterpretation($score, $lang) {
    if ($lang === 'tr') {
        if ($score >= 90) return ['text' => 'Çok Kolay', 'class' => 'success', 'desc' => '5. sınıf seviyesi'];
        if ($score >= 80) return ['text' => 'Kolay', 'class' => 'success', 'desc' => '6. sınıf seviyesi'];
        if ($score >= 70) return ['text' => 'Oldukça Kolay', 'class' => 'info', 'desc' => '7. sınıf seviyesi'];
        if ($score >= 60) return ['text' => 'Standart', 'class' => 'primary', 'desc' => '8-9. sınıf seviyesi'];
        if ($score >= 50) return ['text' => 'Oldukça Zor', 'class' => 'warning', 'desc' => 'Lise seviyesi'];
        if ($score >= 30) return ['text' => 'Zor', 'class' => 'warning', 'desc' => 'Üniversite seviyesi'];
        return ['text' => 'Çok Zor', 'class' => 'danger', 'desc' => 'Lisansüstü seviye'];
    } else {
        if ($score >= 90) return ['text' => 'Very Easy', 'class' => 'success', 'desc' => '5th grade level'];
        if ($score >= 80) return ['text' => 'Easy', 'class' => 'success', 'desc' => '6th grade level'];
        if ($score >= 70) return ['text' => 'Fairly Easy', 'class' => 'info', 'desc' => '7th grade level'];
        if ($score >= 60) return ['text' => 'Standard', 'class' => 'primary', 'desc' => '8th-9th grade level'];
        if ($score >= 50) return ['text' => 'Fairly Difficult', 'class' => 'warning', 'desc' => 'High school level'];
        if ($score >= 30) return ['text' => 'Difficult', 'class' => 'warning', 'desc' => 'College level'];
        return ['text' => 'Very Difficult', 'class' => 'danger', 'desc' => 'Graduate level'];
    }
}

// Form işleme
$result = null;
$error = null;
$text = $_POST['text'] ?? '';

if (!empty($text)) {
    if (mb_strlen($text, 'UTF-8') > 50000) {
        $error = ($currentLang === 'tr') ? 
            'Metin çok uzun! Maksimum 50.000 karakter desteklenir.' :
            'Text too long! Maximum 50,000 characters supported.';
    } else {
        $result = analyzeText($text);
    }
}

include '../includes/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Breadcrumb -->
        <?php
        $breadcrumbItems = [
            ['title' => ($currentLang === 'tr') ? 'Ana Sayfa' : 'Home', 'url' => '/?lang=' . $currentLang],
            ['title' => ($currentLang === 'tr') ? 'Pratik Araçlar' : 'Utility Tools', 'url' => '/' . 'pages/category.php?category=utility'],
            ['title' => $pageTitle]
        ];
        echo generateBreadcrumb($breadcrumbItems);
        ?>

        <!-- Tool Container -->
        <div class="tool-container">
            <div class="tool-header">
                <h1><i class="fas fa-file-alt text-primary"></i> <?php echo $pageTitle; ?></h1>
                <p class="lead"><?php echo $pageDescription; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Text Form -->
                    <div class="tool-form">
                        <form method="POST" id="textForm">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-edit"></i>
                                    <?php echo ($currentLang === 'tr') ? 'Analiz Edilecek Metin' : 'Text to Analyze'; ?>
                                </label>
                                <textarea name="text" class="form-control" rows="12" id="textInput"
                                          placeholder="<?php echo ($currentLang === 'tr') ? 'Metninizi buraya yazın veya yapıştırın...' : 'Type or paste your text here...'; ?>"
                                          oninput="updateLiveStats()"
                                          maxlength="50000"><?php echo htmlspecialchars($text); ?></textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span> / 50,000 
                                    <?php echo ($currentLang === 'tr') ? 'karakter' : 'characters'; ?>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="analyzeBtn">
                                    <i class="fas fa-search"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Analiz Et' : 'Analyze Text'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="clearText()">
                                    <i class="fas fa-eraser"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Temizle' : 'Clear'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Live Counter -->
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="fas fa-tachometer-alt"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Canlı Sayaç' : 'Live Counter'; ?>
                            </h6>
                            <div class="row text-center g-2">
                                <div class="col-6">
                                    <div class="border rounded p-2 bg-light">
                                        <strong id="liveChars" class="text-primary">0</strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'Karakter' : 'Characters'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2 bg-light">
                                        <strong id="liveWords" class="text-info">0</strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'Kelime' : 'Words'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2 bg-light">
                                        <strong id="liveSentences" class="text-success">0</strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'Cümle' : 'Sentences'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2 bg-light">
                                        <strong id="liveParagraphs" class="text-warning">0</strong><br>
                                        <small><?php echo ($currentLang === 'tr') ? 'Paragraf' : 'Paragraphs'; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sample Texts -->
                    <div class="card">
                        <div class="card-body">
                            <h6><?php echo ($currentLang === 'tr') ? 'Örnek Metinler' : 'Sample Texts'; ?></h6>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="loadSampleText('lorem')">
                                    <i class="fas fa-file-text"></i> Lorem Ipsum
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="loadSampleText('article')">
                                    <i class="fas fa-newspaper"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Makale Örneği' : 'Article Example'; ?>
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="loadSampleText('business')">
                                    <i class="fas fa-briefcase"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'İş Metni' : 'Business Text'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <?php if ($result): ?>
                    <!-- Sonuç -->
                    <div class="tool-result">
                        <h4><i class="fas fa-chart-bar"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Analiz Sonucu' : 'Analysis Result'; ?>
                        </h4>
                        <div class="text-result-display">
                            <!-- Temel İstatistikler -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-primary text-white rounded">
                                        <h4><?php echo number_format($result['characters']); ?></h4>
                                        <small><?php echo ($currentLang === 'tr') ? 'Toplam Karakter' : 'Total Characters'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-info text-white rounded">
                                        <h4><?php echo number_format($result['words']); ?></h4>
                                        <small><?php echo ($currentLang === 'tr') ? 'Toplam Kelime' : 'Total Words'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-success text-white rounded">
                                        <h4><?php echo number_format($result['sentences']); ?></h4>
                                        <small><?php echo ($currentLang === 'tr') ? 'Cümle' : 'Sentences'; ?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-warning text-white rounded">
                                        <h4><?php echo number_format($result['paragraphs']); ?></h4>
                                        <small><?php echo ($currentLang === 'tr') ? 'Paragraf' : 'Paragraphs'; ?></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Detaylı Analiz -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Detaylı Analiz' : 'Detailed Analysis'; ?></h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Boşluksuz karakter:' : 'Characters (no spaces):'; ?></td>
                                            <td><strong><?php echo number_format($result['characters_no_spaces']); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Satır sayısı:' : 'Lines:'; ?></td>
                                            <td><strong><?php echo number_format($result['lines']); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo ($currentLang === 'tr') ? 'Benzersiz kelime:' : 'Unique words:'; ?></td>
                                            <td><strong><?php echo number_format($result['unique_words']); ?></strong> 
                                                <small class="text-muted">(<?php echo $result['unique_word_percentage']; ?>%)</small></td>
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
                                            <td><strong><?php echo htmlspecialchars($result['longest_word']); ?></strong> 
                                                <small class="text-muted">(<?php echo mb_strlen($result['longest_word'], 'UTF-8'); ?> harf)</small></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Okuma/Konuşma Süreleri -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Zaman Analizi' : 'Time Analysis'; ?></h6>
                                    <div class="row text-center">
                                        <div class="col-6 mb-2">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['reading_time_average']; ?></strong> 
                                                <?php echo ($currentLang === 'tr') ? 'dk' : 'min'; ?><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Okuma (Ortalama)' : 'Reading (Average)'; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['speaking_time']; ?></strong> 
                                                <?php echo ($currentLang === 'tr') ? 'dk' : 'min'; ?><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Konuşma' : 'Speaking'; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['reading_time_fast']; ?></strong> 
                                                <?php echo ($currentLang === 'tr') ? 'dk' : 'min'; ?><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Hızlı Okuma' : 'Fast Reading'; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 border rounded">
                                                <strong><?php echo $result['reading_time_slow']; ?></strong> 
                                                <?php echo ($currentLang === 'tr') ? 'dk' : 'min'; ?><br>
                                                <small><?php echo ($currentLang === 'tr') ? 'Yavaş Okuma' : 'Slow Reading'; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Okunabilirlik Skoru -->
                            <?php if ($result['flesch_score'] > 0): ?>
                            <?php $readability = getFleschInterpretation($result['flesch_score'], $currentLang); ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'Okunabilirlik Skoru' : 'Readability Score'; ?></h6>
                                    <div class="alert alert-<?php echo $readability['class']; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo $readability['text']; ?></strong><br>
                                                <small><?php echo $readability['desc']; ?></small>
                                            </div>
                                            <span class="badge bg-light text-dark"><?php echo $result['flesch_score']; ?>/100</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar bg-<?php echo $readability['class']; ?>" 
                                                 style="width: <?php echo $result['flesch_score']; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- En Sık Kullanılan Kelimeler -->
                            <?php if (!empty($result['top_words'])): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><?php echo ($currentLang === 'tr') ? 'En Sık Kullanılan Kelimeler' : 'Most Frequent Words'; ?></h6>
                                    <div class="row">
                                        <?php foreach (array_slice($result['top_words'], 0, 8, true) as $word => $count): ?>
                                            <div class="col-6 mb-2">
                                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <span class="text-truncate"><?php echo htmlspecialchars($word); ?></span>
                                                    <span class="badge bg-primary"><?php echo $count; ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-light" onclick="copyResults()">
                                    <i class="fas fa-copy"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Sonuçları Kopyala' : 'Copy Results'; ?>
                                </button>
                                <button class="btn btn-outline-light" onclick="window.print()">
                                    <i class="fas fa-print"></i> 
                                    <?php echo ($currentLang === 'tr') ? 'Yazdır' : 'Print'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php elseif ($error): ?>
                    <!-- Hata -->
                    <div class="tool-result error">
                        <h4><i class="fas fa-exclamation-triangle"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Hata' : 'Error'; ?>
                        </h4>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <?php else: ?>
                    <!-- Placeholder -->
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-lightbulb"></i> 
                                <?php echo ($currentLang === 'tr') ? 'Metin Analizi Hakkında' : 'About Text Analysis'; ?>
                            </h5>
                            <ul>
                                <li><?php echo ($currentLang === 'tr') ? 'Canlı sayaç desteği' : 'Live counter support'; ?></li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Metninizi yazarken canlı sayaç otomatik olarak güncellenir. Maksimum 50.000 karakter analiz edilebilir.' : 
                                        'Live counter updates automatically as you type. Maximum 50,000 characters can be analyzed.'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ad Space -->
        <?php echo renderAdSpace('content', 'large'); ?>

        <!-- Text Analysis Information -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-book"></i> 
                            <?php echo ($currentLang === 'tr') ? 'Metin Analizi Hakkında Detaylı Bilgi' : 'Detailed Information About Text Analysis'; ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Analiz Edilen Metrikler' : 'Analyzed Metrics'; ?></h5>
                                
                                <h6><?php echo ($currentLang === 'tr') ? 'Temel Sayımlar:' : 'Basic Counts:'; ?></h6>
                                <ul>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Karakter:' : 'Characters:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Boşluklar ve noktalama dahil tüm karakterler' : 'All characters including spaces and punctuation'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Kelime:' : 'Words:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Boşluklarla ayrılmış kelime sayısı' : 'Number of words separated by spaces'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Cümle:' : 'Sentences:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Nokta, ünlem ve soru işareti ile ayrılmış' : 'Separated by periods, exclamation and question marks'; ?></li>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Paragraf:' : 'Paragraphs:'; ?></strong> 
                                        <?php echo ($currentLang === 'tr') ? 'Çift satır atlama ile ayrılmış bölümler' : 'Sections separated by double line breaks'; ?></li>
                                </ul>
                                
                                <h6><?php echo ($currentLang === 'tr') ? 'Ortalama Değerler:' : 'Average Values:'; ?></h6>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 'Cümle başına ortalama kelime sayısı' : 'Average words per sentence'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Kelime başına ortalama karakter sayısı' : 'Average characters per word'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 'Paragraf başına ortalama cümle sayısı' : 'Average sentences per paragraph'; ?></li>
                                </ul>
                                
                                <h6><?php echo ($currentLang === 'tr') ? 'Zaman Hesaplamaları:' : 'Time Calculations:'; ?></h6>
                                <ul>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Okuma Hızları:' : 'Reading Speeds:'; ?></strong></li>
                                    <ul>
                                        <li><?php echo ($currentLang === 'tr') ? 'Yavaş: 150 kelime/dakika' : 'Slow: 150 words/minute'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Ortalama: 200 kelime/dakika' : 'Average: 200 words/minute'; ?></li>
                                        <li><?php echo ($currentLang === 'tr') ? 'Hızlı: 250 kelime/dakika' : 'Fast: 250 words/minute'; ?></li>
                                    </ul>
                                    <li><strong><?php echo ($currentLang === 'tr') ? 'Konuşma:' : 'Speaking:'; ?></strong> 130 <?php echo ($currentLang === 'tr') ? 'kelime/dakika' : 'words/minute'; ?></li>
                                </ul>
                            </div>
                            
                            <div class="col-lg-6">
                                <h5><?php echo ($currentLang === 'tr') ? 'Okunabilirlik Analizi' : 'Readability Analysis'; ?></h5>
                                <p>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Flesch Reading Ease skorunu kullanarak metninizin ne kadar kolay okunabilir olduğunu değerlendiririz. Bu skor 0-100 arasında değişir.' :
                                        'We evaluate how easily readable your text is using the Flesch Reading Ease score. This score ranges from 0-100.'; ?>
                                </p>
                                
                                <h6><?php echo ($currentLang === 'tr') ? 'Skor Aralıkları:' : 'Score Ranges:'; ?></h6>
                                <ul class="small">
                                    <li><span class="badge bg-success">90-100:</span> <?php echo ($currentLang === 'tr') ? 'Çok Kolay (5. sınıf)' : 'Very Easy (5th grade)'; ?></li>
                                    <li><span class="badge bg-success">80-89:</span> <?php echo ($currentLang === 'tr') ? 'Kolay (6. sınıf)' : 'Easy (6th grade)'; ?></li>
                                    <li><span class="badge bg-info">70-79:</span> <?php echo ($currentLang === 'tr') ? 'Oldukça Kolay (7. sınıf)' : 'Fairly Easy (7th grade)'; ?></li>
                                    <li><span class="badge bg-primary">60-69:</span> <?php echo ($currentLang === 'tr') ? 'Standart (8-9. sınıf)' : 'Standard (8th-9th grade)'; ?></li>
                                    <li><span class="badge bg-warning">50-59:</span> <?php echo ($currentLang === 'tr') ? 'Oldukça Zor (Lise)' : 'Fairly Difficult (High School)'; ?></li>
                                    <li><span class="badge bg-warning">30-49:</span> <?php echo ($currentLang === 'tr') ? 'Zor (Üniversite)' : 'Difficult (College)'; ?></li>
                                    <li><span class="badge bg-danger">0-29:</span> <?php echo ($currentLang === 'tr') ? 'Çok Zor (Lisansüstü)' : 'Very Difficult (Graduate)'; ?></li>
                                </ul>
                                
                                <h5><?php echo ($currentLang === 'tr') ? 'Kullanım Alanları' : 'Use Cases'; ?></h5>
                                <ul>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'İçerik yazarları için metin optimizasyonu' :
                                        'Text optimization for content writers'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Akademik yazılar için kelime sayımı' :
                                        'Word counting for academic papers'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'SEO içerikleri için analiz' :
                                        'Analysis for SEO content'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Sunum hazırlama için zaman planlaması' :
                                        'Time planning for presentation preparation'; ?></li>
                                    <li><?php echo ($currentLang === 'tr') ? 
                                        'Çeviri projelerinde kelime sayısı' :
                                        'Word count in translation projects'; ?></li>
                                </ul>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt"></i>
                                    <strong><?php echo ($currentLang === 'tr') ? 'Gizlilik:' : 'Privacy:'; ?></strong>
                                    <?php echo ($currentLang === 'tr') ? 
                                        'Metniniz tamamen tarayıcınızda analiz edilir. Hiçbir veri sunucuya gönderilmez.' :
                                        'Your text is analyzed entirely in your browser. No data is sent to the server.'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Tools -->
        <div class="row mt-4">
            <div class="col-12">
                <h4><i class="fas fa-link"></i> 
                    <?php echo ($currentLang === 'tr') ? 'İlgili Araçlar' : 'Related Tools'; ?>
                </h4>
                <div class="row">
                    <?php
                    $relatedTools = [
                        [
                            'name' => ($currentLang === 'tr') ? 'Şifre Üretici' : 'Password Generator',
                            'description' => ($currentLang === 'tr') ? 'Güvenli şifre oluşturun' : 'Generate secure passwords',
                            'url' => '/tools/password-generator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-key'
                        ],
                        [
                            'name' => ($currentLang === 'tr') ? 'QR Kod Üretici' : 'QR Code Generator',
                            'description' => ($currentLang === 'tr') ? 'Farklı türlerde QR kodları oluşturun' : 'Create different types of QR codes',
                            'url' => '/tools/qr-code-generator.php?lang=' . $currentLang,
                            'icon' => 'fas fa-qrcode'
                        ]
                    ];
                    
                    foreach ($relatedTools as $tool): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="<?php echo $tool['icon']; ?>"></i> 
                                        <?php echo $tool['name']; ?>
                                    </h6>
                                    <p class="card-text"><?php echo $tool['description']; ?></p>
                                    <a href="<?php echo $tool['url']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-right"></i> 
                                        <?php echo ($currentLang === 'tr') ? 'Kullan' : 'Use'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Text Analyzer specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('textForm');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const textInput = document.getElementById('textInput');
    const currentLang = '<?php echo $currentLang; ?>';
    
    // Track tool usage
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.analytics.trackEvent('Tool', 'View', 'Text Analyzer');
        
        // Add to recent tools
        const toolName = currentLang === 'tr' ? 'Metin Analizi' : 'Text Analyzer';
        const toolUrl = '/tools/text-analyzer.php?lang=' + currentLang;
        AllInToolbox.storage.addRecentTool('text-analyzer', toolName, toolUrl);
    }
    
    // Initialize live stats
    updateLiveStats();
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const text = textInput.value.trim();
        
        if (text.length > 0) {
            if (typeof AllInToolbox !== 'undefined') {
                AllInToolbox.utils.showLoading(analyzeBtn);
                AllInToolbox.analytics.trackToolUsage('Text Analyzer');
            }
            
            // Simulate processing time
            setTimeout(() => {
                if (typeof AllInToolbox !== 'undefined') {
                    AllInToolbox.utils.showLoading(analyzeBtn, false);
                }
            }, 800);
        }
    });
    
    // Character limit warning
    textInput.addEventListener('input', function() {
        const charCount = this.value.length;
        const charCountElement = document.getElementById('charCount');
        
        if (charCount > 45000) {
            charCountElement.style.color = 'red';
        } else if (charCount > 40000) {
            charCountElement.style.color = 'orange';
        } else {
            charCountElement.style.color = '';
        }
    });
});

// Sample texts
const sampleTexts = {
    lorem: {
        tr: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.

Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.

Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.`,
        en: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.

Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.

Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.`
    },
    article: {
        tr: `Yapay zeka teknolojisi, günümüzün en hızla gelişen alanlarından biridir. Bu teknoloji, insan yaşamının her alanında köklü değişiklikler yaratmaktadır. Sağlık sektöründen eğitime, ulaşımdan finansa kadar birçok alanda yapay zeka uygulamaları kullanılmaktadır.

Yapay zekanın en önemli avantajlarından biri, büyük miktardaki veriyi hızla işleyebilme kapasitesidir. Bu özellik sayesinde, daha önce imkansız olan analizler gerçekleştirilebilmektedir. Örneğin, tıp alanında hastalık teşhisinde yapay zeka sistemleri, radyoloji uzmanlarından daha yüksek doğruluk oranları gösterebilmektedir.

Ancak yapay zekanın gelişimi beraberinde bazı endişeleri de getirmektedir. İş kaybı, gizlilik sorunları ve etik konular bu teknolojinin en çok tartışılan yönleri arasındadır. Bu nedenle, yapay zekanın geliştirilmesi ve kullanılması konusunda dikkatli bir yaklaşım sergilenmesi gerekmektedir.`,
        en: `Artificial intelligence technology is one of the fastest-growing fields of our time. This technology is creating fundamental changes in every area of human life. AI applications are being used in many fields from healthcare to education, from transportation to finance.

One of the most important advantages of artificial intelligence is its capacity to process large amounts of data quickly. Thanks to this feature, analyses that were previously impossible can now be performed. For example, in the medical field, AI systems can show higher accuracy rates than radiology specialists in disease diagnosis.

However, the development of artificial intelligence also brings some concerns. Job losses, privacy issues, and ethical considerations are among the most debated aspects of this technology. Therefore, a careful approach must be taken regarding the development and use of artificial intelligence.`
    },
    business: {
        tr: `Sayın Müşterilerimiz,

Şirketimizin 2024 yılı performansı ve gelecek dönem hedefleri hakkında sizleri bilgilendirmek istiyoruz. Bu yıl içerisinde gerçekleştirdiğimiz yatırımlar ve geliştirme çalışmaları meyvelerini vermeye başlamıştır.

Müşteri memnuniyeti oranımız %95'e ulaşmış, pazar payımız ise %15 artış göstermiştir. Bu başarılı sonuçlar, tüm ekibimizin özverili çalışmasının ve sizlerin desteğinizin sonucudur.

2025 yılında dijital dönüşüm projelerimizi tamamlayarak, müşterilerimize daha kaliteli hizmet sunmayı hedefliyoruz. Yeni teknolojik altyapımız sayesinde, işlem süreleri %40 oranında kısalacaktır.

Desteğiniz için teşekkür ederiz.`,
        en: `Dear Customers,

We would like to inform you about our company's 2024 performance and future goals. The investments and development efforts we have made this year are beginning to bear fruit.

Our customer satisfaction rate has reached 95%, and our market share has increased by 15%. These successful results are the outcome of our entire team's dedicated work and your support.

In 2025, we aim to provide higher quality services to our customers by completing our digital transformation projects. Thanks to our new technological infrastructure, processing times will be reduced by 40%.

Thank you for your support.`
    }
};

// Update live statistics
function updateLiveStats() {
    const text = document.getElementById('textInput').value;
    const chars = text.length;
    const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
    
    // Basic sentence counting for live stats
    const sentences = text.trim() === '' ? 0 : text.split(/[.!?]+/).filter(s => s.trim() !== '').length;
    
    // Basic paragraph counting for live stats
    const paragraphs = text.trim() === '' ? 0 : text.split(/\n\s*\n/).filter(p => p.trim() !== '').length;
    
    document.getElementById('liveChars').textContent = chars.toLocaleString();
    document.getElementById('liveWords').textContent = words.toLocaleString();
    document.getElementById('liveSentences').textContent = sentences.toLocaleString();
    document.getElementById('liveParagraphs').textContent = paragraphs.toLocaleString();
    document.getElementById('charCount').textContent = chars.toLocaleString();
}

// Clear text
function clearText() {
    document.getElementById('textInput').value = '';
    updateLiveStats();
}

// Load sample text
function loadSampleText(type) {
    const lang = '<?php echo $currentLang; ?>';
    const textarea = document.getElementById('textInput');
    
    if (sampleTexts[type] && sampleTexts[type][lang]) {
        textarea.value = sampleTexts[type][lang];
        updateLiveStats();
        
        // Auto-scroll to show the loaded text
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Copy results
function copyResults() {
    <?php if ($result): ?>
    const results = 
        '<?php echo ($currentLang === 'tr') ? 'METİN ANALİZ SONUCU' : 'TEXT ANALYSIS RESULT'; ?>\n' +
        '<?php echo str_repeat('=', 30); ?>\n\n' +
        '<?php echo ($currentLang === 'tr') ? 'TEMEL İSTATİSTİKLER:' : 'BASIC STATISTICS:'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Toplam Karakter: ' : 'Total Characters: '; ?><?php echo number_format($result['characters']); ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Toplam Kelime: ' : 'Total Words: '; ?><?php echo number_format($result['words']); ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Cümle Sayısı: ' : 'Sentences: '; ?><?php echo number_format($result['sentences']); ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Paragraf Sayısı: ' : 'Paragraphs: '; ?><?php echo number_format($result['paragraphs']); ?>\n\n' +
        '<?php echo ($currentLang === 'tr') ? 'ZAMAN ANALİZİ:' : 'TIME ANALYSIS:'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Okuma Süresi: ' : 'Reading Time: '; ?><?php echo $result['reading_time_average']; ?> <?php echo ($currentLang === 'tr') ? 'dakika' : 'minutes'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Konuşma Süresi: ' : 'Speaking Time: '; ?><?php echo $result['speaking_time']; ?> <?php echo ($currentLang === 'tr') ? 'dakika' : 'minutes'; ?>\n\n' +
        '<?php echo ($currentLang === 'tr') ? 'ORTALAMALAR:' : 'AVERAGES:'; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Kelime/Cümle: ' : 'Words/Sentence: '; ?><?php echo $result['avg_words_per_sentence']; ?>\n' +
        '<?php echo ($currentLang === 'tr') ? 'Karakter/Kelime: ' : 'Characters/Word: '; ?><?php echo $result['avg_chars_per_word']; ?>';
    
    if (typeof AllInToolbox !== 'undefined') {
        AllInToolbox.utils.copyToClipboard(results);
    } else {
        navigator.clipboard.writeText(results).then(() => {
            alert('<?php echo ($currentLang === 'tr') ? 'Sonuçlar kopyalandı!' : 'Results copied!'; ?>');
        });
    }
    <?php endif; ?>
}

<?php if ($result): ?>
// Track successful analysis
if (typeof AllInToolbox !== 'undefined') {
    AllInToolbox.analytics.trackEvent('Tool', 'Analyze', 'Text Analyzer');
}
<?php endif; ?>

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to analyze
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('textForm').submit();
    }
    
    // Ctrl/Cmd + Delete to clear
    if ((e.ctrlKey || e.metaKey) && e.key === 'Delete') {
        e.preventDefault();
        clearText();
    }
});
</script>

<?php include '../includes/footer.php'; ?>