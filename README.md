# YouTube Channel Edit

YouTubeの登録チャンネルを効率的に整理・管理するための個人用ツールです。YouTube Data APIを利用して登録チャンネル一覧を取得し、カテゴリ分けや優先度設定を行うことができます。

---


https://github.com/user-attachments/assets/f62e09f7-d643-481a-802e-b05f22f00d1c




## 🎯 主な機能

- **チャンネル同期**: YouTube APIから登録チャンネル一覧を取得し、DBに保存。
- **カテゴリ管理**: チャンネルにカテゴリ（例：プログラミング、ニュース、娯楽など）を割り当て。
- **優先度設定**: 3段階（1: 毎日見る、2: 時々、3: ほぼ見ない）での優先度管理。
- **メモ機能**: チャンネルごとに簡単なメモを記録。
- **ダッシュボード**: 総チャンネル数、優先度別の数、更新状況の把握。

---

## 🏗 技術スタック

- **Framework**: Laravel 12
- **Frontend**: Blade + Tailwind CSS (Alpine.js)
- **Database**: MySQL (または SQLite)
- **API**: YouTube Data API v3

---

## ⚙️ 事前準備：YouTube登録チャンネルの公開設定

YouTubeの「登録チャンネル」を取得するには、自身のYouTubeアカウント側で**登録チャンネルを公開**に設定しておく必要があります。非公開設定のままだと、API経由でデータが取得できません。

1. [YouTube 設定画面（プライバシー）](https://www.youtube.com/account_privacy)へアクセスします。
2. 「すべての登録チャンネルを非公開にする」のスイッチを**オフ**（非表示ではなく公開の状態）にします。
3. 必要に応じて、本ツールでの同期完了後に元の設定に戻すことも可能です。

---

## 🚀 インストール手順

### 1. リポジトリのクローン

```bash
git clone <repository-url>
cd channel-edit-app
```

### 2. バックエンドのセットアップ

`channel-app`ディレクトリに移動し、依存関係をインストールします。

```bash
cd channel-app
composer install
```

### 3. 環境設定

`.env`ファイルを作成し、データベースとYouTube APIの設定を行います。

```bash
cp .env.example .env
php artisan key:generate
```

`.env` 内の以下の項目を適宜設定してください：

- `DB_CONNECTION`, `DB_DATABASE` 等（データベース設定）
- `YOUTUBE_API_KEY`: Google Cloud Consoleで取得したAPIキー
- `YOUTUBE_MY_CHANNEL_ID`: 自身のチャンネルID（登録チャンネル取得用）

### 4. データベースの構築

マイグレーションと初期データの投入を実行します。

```bash
php artisan migrate --seed
```

### 5. フロントエンドのビルド

```bash
npm install
npm run dev
```

---

## 🔐 ログイン情報（開発用）

Seeder実行により、以下のデフォルトアカウントでログイン可能です。

- **メールアドレス**: `admin@example.com`
- **パスワード**: `password`

---

## 🔁 API制限と同期について

- APIのクォータを節約するため、自動同期は行わず、手動の同期ボタンまたはバッチ実行（1日1回程度）を想定しています。
- 重複チェックを行い、既存のチャンネル情報は上書き更新されます。

---

## 📝 ライセンス

個人利用を目的として作成しています。
