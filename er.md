```mermaid
erDiagram
    %% ==========================================
    %% 1. ユーザー（利用者・店舗オーナー共通）
    %% ==========================================
    users {
        bigint id PK
        string nickname "表示名"
        string email "ログインID"
        string password "パスワード"
        string role "利用者/管理者(将来用)"
    }

    %% ==========================================
    %% 2. エリア（住所）
    %% ==========================================
    prefectures ||--|{ cities : "1対多 (県には市がある)"
    
    prefectures {
        bigint id PK
        string name "例: 東京都"
    }
    
    cities {
        bigint id PK
        bigint prefecture_id FK "県ID"
        string name "例: 渋谷区"
        string code "JISコード"
    }

    %% ==========================================
    %% 3. 店舗基本情報
    %% ==========================================
    cities ||--|{ restaurants : "エリア紐付け"
    users ||--o{ restaurants : "オーナー(1人が複数店舗も可)"
    
    restaurants {
        bigint id PK
        bigint user_id FK "オーナーID"
        bigint city_id FK "エリアID"
        string name "店名"
        text description "お店のこだわり等"
        string address_detail "詳細住所(番地・ビル名)"
        string phone_number "電話番号"
        time open_time "開店時間"
        time close_time "閉店時間"
    }

    %% 店舗の公式画像（スライダー用など）
    restaurants ||--o{ restaurant_images : "公式画像"
    restaurant_images {
        bigint id PK
        bigint restaurant_id FK
        string image_url "S3等のパス"
        string alt_text "代替テキスト"
    }

    %% ==========================================
    %% 4. ジャンル・駅（多対多）
    %% ==========================================
    restaurants ||--o{ restaurant_genres : "中間テーブル"
    genres ||--o{ restaurant_genres : "中間テーブル"
    
    genres { bigint id PK string name "和食,ラーメン等" }
    restaurant_genres { bigint id PK bigint restaurant_id FK bigint genre_id FK }

    restaurants ||--o{ restaurant_stations : "中間テーブル"
    stations ||--o{ restaurant_stations : "中間テーブル"
    
    stations { bigint id PK string name "駅名" }
    restaurant_stations { 
        bigint id PK 
        bigint restaurant_id FK 
        bigint station_id FK
        integer minutes_walk "徒歩分数"
        string exit_info "出口情報"
    }

    %% ==========================================
    %% 5. メニュー機能（料理ごとの画像）
    %% ==========================================
    restaurants ||--o{ menu_items : "メニュー登録"
    
    menu_items {
        bigint id PK
        bigint restaurant_id FK
        string name "料理名"
        integer price "価格"
        text description "料理説明"
    }

    menu_items ||--o{ menu_item_images : "1料理に複数画像"
    
    menu_item_images {
        bigint id PK
        bigint menu_item_id FK
        string image_url
        string alt_text "代替テキスト"
    }

    %% ==========================================
    %% 6. レビュー・お気に入り（ユーザーアクション）
    %% ==========================================
    users ||--o{ reviews : "投稿"
    restaurants ||--o{ reviews : "被レビュー"
    
    reviews {
        bigint id PK
        bigint user_id FK
        bigint restaurant_id FK
        integer rating "1~5評価"
        text comment "口コミ本文"
    }
    
    reviews ||--o{ review_images : "口コミ写真"
    review_images { bigint id PK bigint review_id FK string image_url }

    users ||--o{ favorites : "お気に入り"
    restaurants ||--o{ favorites : "お気に入り"
    favorites { bigint id PK bigint user_id FK bigint restaurant_id FK }