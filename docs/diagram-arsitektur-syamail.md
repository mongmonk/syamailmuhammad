# Diagram Arsitektur Sistem Aplikasi Buku Syamail Muhammadiyah

## 1. Arsitektur Sistem secara Keseluruhan

```mermaid
graph TB
    subgraph "Client Layer"
        UI[Web Browser / Mobile Browser]
    end
    
    subgraph "Presentation Layer"
        LR[Laravel Routes]
        C[Laravel Controllers]
        V[Blade Views]
        JS[JavaScript / Alpine.js]
        CSS[Tailwind CSS]
    end
    
    subgraph "Application Layer"
        AS[Application Services]
        LS[Logging Service]
        ES[Encryption Service]
        SS[Search Service]
        CS[Cache Service]
        AS2[Audio Streaming Service]
    end
    
    subgraph "Domain Layer"
        M[Laravel Models]
        R[Repositories]
        E[Domain Entities]
    end
    
    subgraph "Infrastructure Layer"
        PG[(PostgreSQL)]
        RD[(Redis Cache)]
        FS[File Storage]
        MQ[Queue System]
    end
    
    UI --> LR
    LR --> C
    C --> V
    V --> JS
    V --> CSS
    C --> AS
    AS --> LS
    AS --> ES
    AS --> SS
    AS --> CS
    AS --> AS2
    AS --> M
    M --> R
    R --> E
    M --> PG
    CS --> RD
    AS2 --> FS
    AS --> MQ
```

## 2. Arsitektur Database

```mermaid
erDiagram
    users ||--o{ bookmarks : has
    users ||--o{ user_notes : has
    users ||--o{ search_history : has
    chapters ||--o{ hadiths : contains
    hadiths ||--o| audio_files : has
    hadiths ||--o{ bookmarks : has
    hadiths ||--o{ user_notes : has
    
    users {
        bigint id PK
        string name
        string encrypted_email
        string encrypted_phone
        string hashed_password
        timestamp email_verified_at
        string remember_token
        timestamps
    }
    
    chapters {
        bigserial id PK
        string title
        text description
        integer chapter_number
        timestamps
    }
    
    hadiths {
        bigserial id PK
        bigint chapter_id FK
        text arabic_text
        text translation
        text interpretation
        string narration_source
        integer hadith_number
        timestamps
    }
    
    audio_files {
        bigserial id PK
        bigint hadith_id FK
        string file_path
        interval duration
        bigint file_size
        timestamps
    }
    
    bookmarks {
        bigserial id PK
        bigint user_id FK
        bigint hadith_id FK
        text notes
        timestamps
    }
    
    user_notes {
        bigserial id PK
        bigint user_id FK
        bigint hadith_id FK
        text note_content
        timestamps
    }
    
    search_history {
        bigserial id PK
        bigint user_id FK
        string query
        integer results_count
        timestamps
    }
```

## 3. Alur Data untuk Fitur Utama

### 3.1 Alur Data untuk Menampilkan Hadits

```mermaid
sequenceDiagram
    participant U as User
    participant R as Router
    participant C as Controller
    participant M as Model
    participant DB as Database
    participant V as View
    
    U->>R: Request halaman hadits
    R->>C: Panggil HadithController
    C->>M: Model::with(chapter, audioFile)
    M->>DB: Query data hadits
    DB-->>M: Return data hadits
    M-->>C: Return Hadith object
    C->>V: Pass data ke view
    V-->>U: Render halaman hadits
```

### 3.2 Alur Data untuk Enkripsi Data Pengguna

```mermaid
sequenceDiagram
    participant U as User
    participant R as Router
    participant C as Controller
    participant ES as Encryption Service
    participant M as Model
    participant DB as Database
    
    U->>R: Submit form registrasi
    R->>C: Panggil RegisteredUserController
    C->>ES: encrypt(email, phone)
    ES-->>C: Return encrypted data
    C->>M: User::create(encrypted_data)
    M->>DB: Insert encrypted user data
    DB-->>M: Return success
    M-->>C: Return User object
    C-->>U: Redirect ke halaman sukses
```

### 3.3 Alur Data untuk Pencarian Hadits

```mermaid
sequenceDiagram
    participant U as User
    participant R as Router
    participant C as Controller
    participant CS as Cache Service
    participant SS as Search Service
    participant M as Model
    participant DB as Database
    participant SH as Search History
    
    U->>R: Submit form pencarian
    R->>C: Panggil SearchController
    C->>CS: getSearchResults(query, page)
    CS->>CS: Check cache
    alt Cache tersedia
        CS-->>C: Return cached results
    else Cache tidak tersedia
        CS->>SS: search(query, page)
        SS->>M: Hadith::search(query)
        M->>DB: Full-text search
        DB-->>M: Return search results
        M-->>SS: Return Hadith collection
        SS-->>CS: Return search results
        CS->>CS: Store results in cache
        CS-->>C: Return search results
    end
    C->>SH: saveSearchHistory(query, userId, resultsCount)
    SH->>DB: Insert search history
    C-->>U: Display search results
```

### 3.4 Alur Data untuk Streaming Audio dengan Lazy Loading

```mermaid
sequenceDiagram
    participant U as User
    participant JS as JavaScript
    participant R as Router
    participant AC as AudioController
    participant AS as Audio Streaming Service
    participant FS as File Storage
    
    U->>JS: Klik tombol play audio
    JS->>R: Request audio URL
    R->>AC: getAudioUrl(audioId)
    AC->>AS: getAudioUrl(audioFile)
    AS-->>AC: Return audio URL
    AC-->>R: Return JSON response
    R-->>JS: Return audio URL
    JS->>JS: Create audio element with URL
    JS->>U: Display audio player
    U->>JS: Klik play pada audio player
    JS->>R: Request audio stream
    R->>AC: stream(audioFile)
    AC->>AS: streamAudio(audioFile)
    AS->>FS: Read audio file with range support
    FS-->>AS: Return audio stream
    AS-->>AC: Stream audio response
    AC-->>R: Stream audio response
    R-->>JS: Stream audio data
    JS-->>U: Play audio
```

## 4. Arsitektur Komponen Frontend

```mermaid
graph TD
    subgraph "Layout Components"
        A[App Layout]
        B[Header]
        C[Footer]
        D[Sidebar]
    end
    
    subgraph "Page Components"
        E[Chapter List]
        F[Chapter Detail]
        G[Hadith Detail]
        H[Search Form]
        I[Search Results]
        J[Bookmark List]
        K[User Profile]
    end
    
    subgraph "UI Components"
        L[Audio Player]
        M[Search Box]
        N[Bookmark Button]
        O[Note Editor]
        P[Pagination]
    end
    
    subgraph "JavaScript Modules"
        Q[audio-player.js]
        R[search.js]
        S[bookmark.js]
        T[note-editor.js]
    end
    
    A --> B
    A --> C
    A --> D
    E --> A
    F --> A
    G --> A
    H --> A
    I --> A
    J --> A
    K --> A
    G --> L
    H --> M
    G --> N
    G --> O
    E --> P
    F --> P
    I --> P
    L --> Q
    H --> R
    G --> S
    G --> T
```

## 5. Arsitektur Keamanan

```mermaid
graph TB
    subgraph "Security Layers"
        SL[SSL/TLS Encryption]
        SH[Security Headers]
        RL[Rate Limiting]
        AU[Authentication]
        AZ[Authorization]
        DE[Data Encryption]
        VA[Input Validation]
        CS[CSRF Protection]
        SQ[SQL Injection Protection]
        XSS[XSS Protection]
    end
    
    subgraph "User Data Protection"
        UE[Email Encryption]
        UP[Phone Encryption]
        PP[Password Hashing]
        ST[Session Token]
    end
    
    subgraph "Application Security"
        SC[Sanitization]
        FW[Firewall Rules]
        AL[Activity Logging]
        ER[Error Handling]
    end
    
    SL --> SH
    SH --> RL
    RL --> AU
    AU --> AZ
    AZ --> DE
    DE --> VA
    VA --> CS
    CS --> SQ
    SQ --> XSS
    DE --> UE
    DE --> UP
    DE --> PP
    DE --> ST
    VA --> SC
    SC --> FW
    FW --> AL
    AL --> ER
```

## 6. Arsitektur Caching

```mermaid
graph LR
    subgraph "Cache Strategy"
        RC[Redis Cache]
        DC[Database Cache]
        FC[File Cache]
    end
    
    subgraph "Cache Types"
        CC[Chapter Cache]
        HC[Hadith Cache]
        SC[Search Cache]
        UC[User Data Cache]
        AC[Audio Metadata Cache]
    end
    
    subgraph "Cache Operations"
        G[Get]
        S[Set]
        D[Delete]
        F[Flush]
        R[Remember]
        FR[Forget]
    end
    
    RC --> CC
    RC --> HC
    RC --> SC
    DC --> UC
    FC --> AC
    
    CC --> G
    CC --> S
    CC --> D
    HC --> G
    HC --> S
    HC --> D
    SC --> G
    SC --> S
    SC --> D
    UC --> G
    UC --> S
    UC --> D
    AC --> G
    AC --> S
    AC --> D
    
    RC --> F
    DC --> F
    FC --> F
    
    G --> R
    S --> R
    D --> FR
```

## 7. Deployment Architecture

```mermaid
graph TB
    subgraph "Production Environment"
        subgraph "Web Server"
            NG[Nginx]
            LS[Laravel]
        end
        
        subgraph "Application Server"
            PM[PHP-FPM]
            SC[Supervisor]
        end
        
        subgraph "Database Server"
            PG2[(PostgreSQL)]
            RD2[(Redis)]
        end
        
        subgraph "File Storage"
            S3[Amazon S3 / Local Storage]
        end
        
        subgraph "Monitoring"
            MON[Monitoring Tools]
            LOG[Logging System]
        end
    end
    
    subgraph "CDN"
        CDN[Content Delivery Network]
    end
    
    subgraph "DNS"
        DNS[DNS Management]
    end
    
    DNS --> NG
    NG --> PM
    PM --> LS
    LS --> PG2
    LS --> RD2
    LS --> S3
    SC --> LS
    MON --> NG
    MON --> PM
    MON --> PG2
    MON --> RD2
    LOG --> LS
    LOG --> PM
    LOG --> PG2
    S3 --> CDN
```

## 8. Microservices Architecture (Future Enhancement)

```mermaid
graph TB
    subgraph "API Gateway"
        GW[API Gateway]
    end
    
    subgraph "User Service"
        US[User Service]
        UDB[(User DB)]
    end
    
    subgraph "Content Service"
        CS[Content Service]
        CDB[(Content DB)]
    end
    
    subgraph "Search Service"
        SS[Search Service]
        SE[Search Engine]
    end
    
    subgraph "Audio Service"
        AS[Audio Service]
        ADB[(Audio DB)]
        FS2[File Storage]
    end
    
    subgraph "Notification Service"
        NS[Notification Service]
    end
    
    GW --> US
    GW --> CS
    GW --> SS
    GW --> AS
    GW --> NS
    
    US --> UDB
    CS --> CDB
    SS --> SE
    AS --> ADB
    AS --> FS2