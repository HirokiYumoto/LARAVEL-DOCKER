## 予約システム ER図（IE記法）

### 概要
店舗（Restaurant）は複数の座席タイプ（RestaurantSeatType）を持ち、予約（Reservation）は特定の座席タイプを指定して行われる設計です。
これにより、「カウンター席」や「テーブル席」ごとの在庫管理と予約受付を実現します。

```mermaid
erDiagram
    %% 定員ルールを管理する親テーブル
    restaurants {
        bigint id PK
        string name "店名"
        text description "説明"
        string address "住所"
        string city_id FK "エリア"
        string user_id FK "オーナー"
        %% 全体のcapacityカラムは削除または「総定員」として参考値扱いに変更
        datetime created_at
        datetime updated_at
    }

    %% 【新規】店舗ごとの座席タイプ設定（マスタ）
    restaurant_seat_types {
        bigint id PK
        bigint restaurant_id FK "所属店舗"
        string name "席名称 (例:カウンター, テーブル)"
        int capacity "そのタイプの定員数 (席数/卓数)"
        datetime created_at
        datetime updated_at
    }

    %% 予約実績テーブル
    reservations {
        bigint id PK
        bigint user_id FK "予約者"
        bigint restaurant_id FK "予約店舗"
        bigint restaurant_seat_type_id FK "予約した席タイプ"
        datetime reserved_at "予約日時"
        int number_of_people "予約人数"
        datetime created_at
        datetime updated_at
    }

    %% リレーション定義
    restaurants ||--|{ restaurant_seat_types : "1店舗は複数の席タイプを持つ"
    restaurants ||--o{ reservations : "1店舗は複数の予約を持つ"
    
    %% 重要なリレーション：予約は特定の席タイプに紐づく
    restaurant_seat_types ||--o{ reservations : "1つの席タイプに複数の予約が入る"