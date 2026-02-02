<?php

namespace App\Services;

class MecabService
{
    /**
     * テキストをカタカナ（読み仮名）に変換する
     *
     * @param string $text 変換したい日本語テキスト
     * @return string カタカナに変換されたテキスト
     */
    public function toKatakana(string $text): string
    {
        // 空文字ならそのまま返す
        if (empty($text)) {
            return '';
        }

        // コマンドライン用の安全な文字列にエスケープ（セキュリティ対策）
        $escapedText = escapeshellarg($text);

        // MeCabコマンドを実行
        // echo "テキスト" | mecab -O yomi
        $command = "echo {$escapedText} | mecab -O yomi";
        
        // コマンドを実行して結果を受け取る
        $output = shell_exec($command);

        // 余計な改行や空白を削除して返す
        return trim($output ?? '');
    }
}