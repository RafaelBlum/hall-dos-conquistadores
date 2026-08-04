# Migrations — RetroAchievements Laravel Integration

## 1. create_ra_profiles_table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ra_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('ra_username')->unique();
            $table->unsignedBigInteger('ra_user_id')->nullable()->unique();
            $table->string('display_name')->nullable();
            $table->string('motto')->nullable();
            $table->string('avatar_url')->nullable();
            $table->unsignedBigInteger('total_points')->default(0);
            $table->unsignedBigInteger('total_softcore')->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->unsignedSmallInteger('mastery_count')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index('channel_id');
            $table->index('ra_username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ra_profiles');
    }
};
```

## 2. create_ra_games_table

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ra_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ra_game_id')->unique(); // ID no RetroAchievements
            $table->string('title');
            $table->string('console_name')->nullable();
            $table->unsignedSmallInteger('console_id')->nullable();
            $table->string('genre')->nullable();
            $table->string('developer')->nullable();
            $table->string('publisher')->nullable();
            $table->string('release_date')->nullable();
            $table->string('image_icon_url')->nullable();
            $table->string('image_box_art_url')->nullable();
            $table->string('ra_url')->nullable();
            $table->unsignedSmallInteger('achievement_count')->default(0);
            $table->unsignedInteger('points')->default(0);
            $table->boolean('is_in_project_pool')->default(false); // Está no pool do projeto?
            $table->text('notes')->nullable(); // Notas do admin sobre o jogo
            $table->timestamps();

            $table->index('ra_game_id');
            $table->index('is_in_project_pool');
            $table->index('console_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ra_games');
    }
};
```

## 3. create_ra_user_games_table

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ra_user_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ra_game_id');
            $table->unsignedSmallInteger('achievements_earned')->default(0);
            $table->unsignedSmallInteger('achievements_earned_hardcore')->default(0);
            $table->unsignedSmallInteger('achievements_total')->default(0);
            $table->unsignedInteger('score_achieved')->default(0);
            $table->unsignedInteger('score_achieved_hardcore')->default(0);
            $table->decimal('completion_percent', 5, 2)->default(0.00);
            $table->decimal('completion_percent_hardcore', 5, 2)->default(0.00);
            $table->boolean('is_mastered')->default(false);
            $table->boolean('is_mastered_hardcore')->default(false);
            $table->timestamp('last_played_at')->nullable();
            $table->timestamps();

            $table->unique(['channel_id', 'ra_game_id']);
            $table->index('channel_id');
            $table->index('ra_game_id');
            $table->index('is_mastered');

            $table->foreign('ra_game_id')
                ->references('ra_game_id')
                ->on('ra_games')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ra_user_games');
    }
};
```

## 4. create_project_challenges_table

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ra_game_id');
            $table->enum('status', ['active', 'mastered', 'paused', 'cancelled'])->default('active');
            $table->unsignedSmallInteger('progress_earned')->default(0);
            $table->unsignedSmallInteger('progress_total')->default(0);
            $table->decimal('progress_percent', 5, 2)->default(0.00);
            $table->unsignedInteger('score_achieved')->default(0);
            $table->timestamp('drawn_at')->nullable();      // Quando foi sorteado
            $table->timestamp('started_at')->nullable();    // Quando o canal iniciou
            $table->timestamp('completed_at')->nullable();  // Quando masterizou
            $table->timestamp('deadline_at')->nullable();   // Prazo (opcional)
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('channel_id');
            $table->index('ra_game_id');
            $table->index('status');
            $table->index(['channel_id', 'status']);

            $table->foreign('ra_game_id')
                ->references('ra_game_id')
                ->on('ra_games')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_challenges');
    }
};
```

## 5. create_project_masteries_table

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_masteries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_challenge_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ra_game_id');
            $table->unsignedInteger('points_earned')->default(0);
            $table->boolean('is_hardcore')->default(false);
            $table->timestamp('mastered_at');
            $table->timestamps();

            $table->unique(['channel_id', 'project_challenge_id']);
            $table->index('channel_id');
            $table->index('ra_game_id');
            $table->index('mastered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_masteries');
    }
};
```

## Ordem de execução das migrations

```
1. ra_games                    (sem dependências)
2. ra_profiles                 (depende de channels)
3. ra_user_games               (depende de channels e ra_games)
4. project_challenges          (depende de channels e ra_games)
5. project_masteries           (depende de channels e project_challenges)
```

## Factories para testes

```php
// RaGameFactory
RaGame::factory()->definition([
    'ra_game_id'          => fake()->unique()->numberBetween(1, 99999),
    'title'               => fake()->words(3, true),
    'console_name'        => fake()->randomElement(['Mega Drive', 'SNES', 'PlayStation', 'Game Boy']),
    'achievement_count'   => fake()->numberBetween(10, 100),
    'points'              => fake()->numberBetween(100, 2000),
    'is_in_project_pool'  => false,
]);

// ProjectChallengeFactory
ProjectChallenge::factory()->definition([
    'status'           => 'active',
    'progress_earned'  => fake()->numberBetween(0, 50),
    'progress_total'   => 100,
    'progress_percent' => fn(array $attrs) => $attrs['progress_earned'],
    'drawn_at'         => fake()->dateTimeBetween('-3 months', 'now'),
]);
```
