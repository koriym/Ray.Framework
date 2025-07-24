# ドキュメントとしてのアーキテクチャ：自己記述システムの究極形

> 「コードがドキュメントである」— マーティン・ファウラー  
> 「アーキテクチャ**が**ドキュメントである」— Ray.Framework

## 序文：自己記述システムの進化

この文書は、**自己記述システムの究極形**を提示している。マーティン・ファウラーの「コードとしてのドキュメント」概念を論理的結論まで推し進め、「アーキテクチャとしてのドキュメント」という革命的なアプローチを実現している。これは単なる技術的改善ではない。これは**ドキュメンテーションの哲学的転回**である。

## 第1章：従来の境界を超えて

### 従来のコードとしてのドキュメント

```php
// 従来：コードが何をするかを説明
class UserValidator {
    /**
     * ユーザーメールフォーマットを検証
     * 
     * @param  string $email 検証するメール
     * @return bool          有効な場合true
     */
    public function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

### ドキュメントとしてのアーキテクチャ

```php
// Ray.Framework：アーキテクチャが何が存在するかを説明
#[Be([Success::class, Failure::class])]
final class ValidationAttempt {
    public readonly Success|Failure $being;
}
```

この対比は**記述の質的転換**を示している。従来のコメントが「実装の説明」であるのに対し、アーキテクチャ自体が「存在の宣言」となっている：

- **何が存在できるか** (`Success`, `Failure`)
- **何の関係が存在するか** (`#[Be]` 属性)  
- **何のデータが流れるか** (`Success|Failure $being`)
- **何の契約が統制するか** (ユニオン型)

## 第2章：ray-tree コマンドの革新性

### 基本構造の可視化

```bash
ray-tree src/UserRegistration/
# アーキテクチャ構造可視化を出力
UserRegistration/
├── RegistrationInput (#[Be] → ValidatedRegistration)
│   ├── 📥 email: string (validates/ValidEmail)
│   └── 📥 password: string (validates/StrongPassword)
├── ValidatedRegistration (#[Be] → Success|Conflict)
│   ├── 📤 being: NewUser|ConflictingUser
│   └── 🔄 UserRepository経由で運命を自己決定
└── Outcomes/
    ├── UnverifiedUser (🦋 Success path)
    │   ├── userId: string
    │   └── verificationToken: string
    └── UserConflict (🦋 Conflict path)
        └── message: string
```

この可視化は**アーキテクチャの生態系**を表現している。各シンボルが意味を持つ：
- 📥：入力データ
- 📤：出力データ
- 🔄：自己決定プロセス
- 🦋：変容の結果

### セマンティック分析モード

```bash
$ ray-tree --semantic src/
📋 セマンティック変数レジストリ
├── email (validates/ValidEmail.php)
│   ├── 📖 ALPS: RFC 5322準拠のメールアドレス
│   ├── 🔍 使用場所: RegistrationInput, UserProfile, EmailValidation
│   └── ✅ 検証: メール形式、ドメインチェック
├── password (validates/StrongPassword.php)  
│   ├── 📖 ALPS: 複雑さ要件を満たすセキュアパスワード
│   └── ✅ 検証: 長さ≥8、複雑さルール
└── age (validates/NonNegativeAge.php)
    ├── 📖 ALPS: 人の年齢（年単位）、非負整数
    └── ✅ 検証: >= 0、整数型
```

この機能の革新性は、**意味論的整合性の自動検証**にある。変数名が単なるラベルではなく、明確に定義された**意味的実体**として扱われている。

### フロー可視化モード

```bash
$ ray-tree --flow UserRegistration
🌊 UserRegistrationのメタモルフォーシスフロー

RegistrationInput
    ↓ #[Be]
ValidatedRegistration  
    ↓ #[Be] (自己発見)
┌─ Success → UnverifiedUser → VerificationEmailSent
└─ Conflict → UserConflict

💡 決定ポイント:
- ValidatedRegistration.being: NewUser|ConflictingUser
  ├── NewUser → Success path (メール利用可能)  
  └── ConflictingUser → Conflict path (メール存在)
```

これは**業務フローの哲学的実装**である。決定が外部から強制されるのではなく、オブジェクト自身の**自己発見プロセス**として表現されている。

## 第3章：ドキュメントとしてのアーキテクチャの三つの柱

### 1. 構造的ドキュメント（自動）

`#[Be]`属性によるデータフローの完全マップ作成：

```php
#[Be(ProcessedData::class)]          // "私はProcessedDataになる"
#[Be([Success::class, Failure::class])] // "私はSuccessかFailureになりうる"
```

**結果**: コードから自動生成される完全なアーキテクチャ図。

この仕組みは**意図の技術的実装**を表している。プログラマーの意図が属性として明示化され、それが自動的にドキュメントに変換される。

### 2. セマンティックドキュメント（ALPS統合）

```json
// alps/email.json - 意味の単一情報源
{
    "alps": {
        "descriptor": [{
            "id": "email",
            "type": "semantic",
            "doc": {"value": "RFC 5322に準拠した有効なメールアドレス"}
        }]
    }
}
```

**結果**: すべての変数名が定義され、発見可能な意味を持つ。

これは**意味論の技術的実装**である。ドメイン知識が技術仕様から分離され、独立したセマンティックレイヤーとして管理されている。

### 3. 検証ドキュメント（慣習ベース）

```
validates/
├── ValidEmail.php      → メールの有効性を定義
├── NonNegativeAge.php  → 年齢制約を定義
└── PositivePrice.php   → 価格要件を定義
```

**結果**: すべてのビジネスルールが明示的、テスト可能、文書化されている。

これは**ビジネスロジックの宣言的実装**である。検証ルールがコード内に埋もれるのではなく、独立したファイルとして明確に宣言されている。

## 第4章：Mermaid図生成の革新性

```bash
$ ray-tree --mermaid src/UserRegistration/ > registration-flow.md
```

生成されるMermaid図：

```mermaid
flowchart TD
    A[RegistrationInput] -->|#[Be]| B[ValidatedRegistration]
    B -->|#[Be]| C{being: NewUser|ConflictingUser}
    C -->|NewUser| D[UnverifiedUser]
    C -->|ConflictingUser| E[UserConflict]
    D -->|#[Be]| F[VerificationEmailSent]
    E -->|#[Be]| G[JsonResponse 409]
    F -->|#[Be]| H[JsonResponse 201]
    
    subgraph "セマンティック変数"
        I[email: ValidEmail.php]
        J[password: StrongPassword.php]
    end
    
    subgraph "ALPS定義"
        K[alps/email.json]
        L[alps/password.json]
    end
    
    I -.-> K
    J -.-> L
```

この自動生成図は、**アーキテクチャの生きた表現**である。静的な設計図ではなく、コードの現在状態を反映する**動的ドキュメント**となっている。

## 第5章：実装コンセプトの技術的深度

```php
class RayTreeAnalyzer
{
    public function analyze(string $path): ArchitectureMap
    {
        $classes = $this->discoverClasses($path);
        $flows = $this->extractFlows($classes);        // #[Be] 属性
        $semantics = $this->loadALPS();               // alps/*.json ファイル
        $validations = $this->discoverValidators();   // validates/*.php
        
        return new ArchitectureMap($classes, $flows, $semantics, $validations);
    }
    
    private function extractFlows(array $classes): array
    {
        $flows = [];
        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);
            
            // #[Be] 宛先と可能性を抽出
            $beAttributes = $reflection->getAttributes(Be::class);
            
            // $beingプロパティのユニオン型を分析
            $beingProperty = $reflection->getProperty('being');
            $unionTypes = $this->parseUnionTypes($beingProperty);
            
            $flows[$class] = new FlowDefinition($beAttributes, $unionTypes);
        }
        return $flows;
    }
}
```

この実装は**メタプログラミングの活用**を示している。リフレクションを使用してコードの構造を分析し、それを意味のあるドキュメントに変換する仕組みが構築されている。

## 第6章：革命的インパクト

### 従来のドキュメンテーション問題の根本的解決

| 問題 | 従来の解決策 | Architecture as Documentation |
|------|-------------|-------------------------------|
| **時代遅れ** | 手動更新 | **コードから自動生成** |
| **不完全** | 追加文書作成 | **完全なカバレッジ** |
| **分散** | 統合ツール | **単一コマンドで全体表示** |
| **手動** | プロセス改善 | **完全自動化** |

### ステークホルダー別利益

**開発者にとって**:
- **即座の理解**: 新チームメンバーが完全なアーキテクチャを即座に把握
- **設計検証**: アーキテクチャ問題の即座の可視化
- **安全なリファクタリング**: システム全体への変更影響の表示

**プロダクトマネージャーにとって**:
- **ビジネスフロー明確性**: データがビジネスプロセス内をどう移動するかの視覚化
- **決定ポイント**: ビジネスルールが適用される場所の理解
- **機能インパクト**: 変更が既存フローに与える影響の可視化

**アーキテクトにとって**:
- **システム概観**: 数秒でのアーキテクチャ全体像の把握
- **依存関係分析**: 結合度と凝集度の明確な視覚化
- **パターン準拠**: アーキテクチャ原則への準拠の検証

## 第7章：未来の可能性

### IDE統合の展望

```typescript
// VS Code拡張
ray.framework.generateArchitecture({
    path: './src',
    format: 'mermaid',
    includeSemantics: true
});
```

### CI/CD統合

```yaml
# GitHub Actions
- name: アーキテクチャドキュメント生成
  run: ray-tree --mermaid src/ > docs/architecture.md
  
- name: セマンティック整合性検証
  run: ray-tree --validate-semantics src/
```

### リアルタイムドキュメント

```php
// ライブドキュメントサーバー
$server = new ArchitectureDocServer();
$server->watch('./src')->generateOnChange();
// コードが変更されるとドキュメントがリアルタイムで更新
```

## 結論：ドキュメンテーションの哲学的転回

Architecture as Documentationは、自己記述システムの自然な進化を表している。属性、ユニオン型、命名規則を通じてアーキテクチャ意図を直接コード構造に埋め込むことで、Ray.Frameworkは以下のようなシステムを創造している：

- **アーキテクチャが自己文書化する**
- **ドキュメントが常に最新**  
- **視覚図が自動生成される**
- **ビジネス意味論が明示的**
- **システム理解が即座**

これは単なる改善されたドキュメンテーションではない—これはシステム設計について考える方法の**根本的転換**である。アーキテクチャがドキュメントになり、ドキュメントがアーキテクチャになる時、意図と実装の間に完璧な整合を達成する。

## 総合考察：情報アーキテクチャの革命

この文書の真の価値は、**情報アーキテクチャの革命**を提示していることにある。これは単なる自動化ツールの紹介ではない。これは**ドキュメンテーションの存在論的転回**を表現している。

### ドキュメントの実体化

従来のドキュメントは**表現**だった—システムについて語る外部的記述。Architecture as Documentationでは、ドキュメントが**実体**となる—システムの内在的構造そのもの。

### 意図の技術的実装

`#[Be]`属性は単なるメタデータではない。これは**プログラマーの意図の技術的実装**である。設計思想がコードの構造レベルで表現され、それが自動的にドキュメントに変換される。

### セマンティクスの分離と統合

ALPS統合による意味論の管理は、**ドメイン知識の技術的独立性**を実現している。ビジネスロジックが技術実装から分離され、しかも完全に統合されている。

### メタプログラミングの活用

`RayTreeAnalyzer`の実装は、**リフレクションによる自己認識**の技術的実現である。システムが自己の構造を分析し、それを意味のある形で表現する能力を持つ。

### 時間的整合性の保証

コードから自動生成されるドキュメントは、**時間的整合性の技術的保証**を提供している。ドキュメントとコードの乖離という古典的問題が構造的に解決されている。

この文書は、情報システムにおける**自己記述能力の究極的実現**を示している。システムが単に機能するだけでなく、自己の構造と意味を明確に表現できる未来への道筋を具体的に提示している。これは、ソフトウェア工学における**メタ認知的システム**の先駆的実装として、極めて革新的な価値を持つ。