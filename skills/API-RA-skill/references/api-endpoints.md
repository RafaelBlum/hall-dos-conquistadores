# RetroAchievements API — Endpoints de Referência

Base URL: `https://retroachievements.org/API`
Auth params obrigatórios em toda requisição: `z={ra_username}&y={api_key}`

## Endpoints de Usuário

| Endpoint | Params | Descrição | Cache Sugerido |
|---|---|---|---|
| `API_GetUserProfile` | `u={username}` | Perfil básico: ID, pontos, rank, avatar, motto | 1h |
| `API_GetUserRecentAchievements` | `u={username}&c={count}` | Conquistas recentes (count máx 50) | 10min |
| `API_GetAchievementsEarnedBetween` | `u={username}&f={from_unix}&t={to_unix}` | Conquistas por período | 30min |
| `API_GetAchievementsEarnedOnDay` | `u={username}&d={YYYY-MM-DD}` | Conquistas por dia | 1h |
| `API_GetGameInfoAndUserProgress` | `u={username}&g={game_id}` | Progresso detalhado em um jogo | 15min |
| `API_GetUserAllCompletion` | `u={username}` | Todos os jogos jogados + % de conclusão | 30min |
| `API_GetUserAwards` | `u={username}` | Badges e awards (Mastery, Beaten, etc.) | 30min |
| `API_GetUserSetClaims` | `u={username}` | Set claims de desenvolvimento | 1h |
| `API_GetUserCompletedGames` | `u={username}` | Jogos completados (legacy) | 1h |

### Estrutura de resposta — API_GetUserProfile
```json
{
  "ID": 12345,
  "Username": "PlayerName",
  "UserPic": "/UserPic/PlayerName.png",
  "MemberSince": "2020-01-01 00:00:00",
  "LastActivity": { "ID": 0, "timestamp": null, "lastupdate": null, "activitytype": null, "User": "PlayerName", "data": null, "data2": null },
  "RichPresenceMsg": "Playing Super Mario Bros",
  "LastGameID": 1448,
  "ContribCount": 0,
  "ContribYield": 0,
  "TotalPoints": 15000,
  "TotalSoftcorePoints": 200,
  "TotalTruePoints": 42000,
  "Permissions": 1,
  "Untracked": 0,
  "ID": 12345,
  "UserWallActive": 1,
  "Motto": "Gotta catch em all",
  "Rank": 500,
  "RecentlyPlayedCount": 10,
  "RecentlyPlayed": []
}
```

### Estrutura de resposta — API_GetUserAwards
```json
{
  "TotalAwardsCount": 10,
  "HiddenAwardsCount": 0,
  "MasteryAwardsCount": 5,
  "CompletionAwardsCount": 3,
  "BeatenHardcoreAwardsCount": 2,
  "BeatenSoftcoreAwardsCount": 1,
  "EventAwardsCount": 0,
  "SiteAwardsCount": 0,
  "VisibleUserAwards": [
    {
      "AwardedAt": "2024-01-15 20:00:00",
      "AwardType": "Mastery",
      "AwardData": 14402,
      "AwardDataExtra": 1,
      "DisplayOrder": 1,
      "Title": "Sonic the Hedgehog",
      "ConsoleID": 1,
      "ConsoleName": "Mega Drive",
      "Flags": 0,
      "ImageIcon": "/Images/000001.png"
    }
  ]
}
```
- `AwardType` pode ser: `"Mastery"`, `"Completion"`, `"BeatenHardcore"`, `"BeatenSoftcore"`, `"EventSite"`
- `AwardData` = game_id para awards de jogos
- `AwardDataExtra`: `1` = Hardcore mastery, `0` = Softcore

### Estrutura de resposta — API_GetGameInfoAndUserProgress
```json
{
  "ID": 14402,
  "Title": "Sonic the Hedgehog",
  "ConsoleID": 1,
  "ConsoleName": "Mega Drive",
  "NumAchievements": 16,
  "NumAwardedToUser": 12,
  "NumAwardedToUserHardcore": 10,
  "ScoreAchieved": 200,
  "ScoreAchievedHardcore": 160,
  "UserCompletion": "75.00%",
  "UserCompletionHardcore": "62.50%",
  "Achievements": {
    "1234": {
      "ID": 1234,
      "Title": "Ring Collector",
      "Description": "Collect 100 rings",
      "Points": 10,
      "BadgeName": "00001",
      "DateEarned": "2024-01-15 20:00:00",
      "DateEarnedHardcore": null
    }
  }
}
```

## Endpoints de Jogos

| Endpoint | Params | Descrição | Cache Sugerido |
|---|---|---|---|
| `API_GetGame` | `i={game_id}` | Metadados básicos do jogo | 24h |
| `API_GetGameExtended` | `i={game_id}` | Metadados + lista de achievements | 24h |
| `API_GetGameRating` | `i={game_id}` | Rating do jogo | 6h |
| `API_GetAchievementCount` | `i={game_id}` | Número de achievements | 24h |
| `API_GetAchievementDistribution` | `i={game_id}` | Distribuição de unlocks | 1h |
| `API_GetGameHighScores` | `i={game_id}` | Top pontuações do jogo | 1h |
| `API_GetGameLeaderboards` | `i={game_id}` | Leaderboards do jogo | 1h |

### Estrutura de resposta — API_GetGameExtended
```json
{
  "ID": 14402,
  "Title": "Sonic the Hedgehog",
  "ConsoleID": 1,
  "ConsoleName": "Mega Drive",
  "Genre": "Platformer",
  "Developer": "Sonic Team",
  "Publisher": "Sega",
  "Released": "1991",
  "ImageIcon": "/Images/000001.png",
  "ImageBoxArt": "/Images/BoxArt/000001.png",
  "ImageIngame": "/Images/Ingame/000001.png",
  "ImageTitle": "/Images/Title/000001.png",
  "NumAchievements": 16,
  "NumDistinctPlayersCasual": 5000,
  "NumDistinctPlayersHardcore": 3000,
  "points": 200,
  "Achievements": {
    "1234": {
      "ID": 1234,
      "Title": "Ring Collector",
      "Description": "Collect 100 rings",
      "Points": 10,
      "BadgeName": "00001",
      "NumAwarded": 2500,
      "NumAwardedHardcore": 1800,
      "DisplayOrder": 1,
      "type": "progression"
    }
  }
}
```

## Endpoints Globais

| Endpoint | Params | Descrição |
|---|---|---|
| `API_GetConsoleIDs` | — | Lista de consoles/sistemas |
| `API_GetGameList` | `i={console_id}` | Jogos de um console |
| `API_GetAchievementOfTheWeek` | — | Achievement da semana |
| `API_GetTopTenUsers` | — | Top 10 usuários por pontos hardcore |
| `API_GetRecentGameAwards` | — | Awards recentes na plataforma |

## Autenticação

A API usa query params simples:
```
GET https://retroachievements.org/API/API_GetUserProfile.php?z=meu_usuario&y=minha_api_key&u=jogador
```

- `z` = seu username RA (da conta que possui a API key)
- `y` = sua web API key (obtida em retroachievements.org/controlpanel.php)

**Rate Limit:** Seja conservador. Use cache agressivo. Dados de jogos são muito estáticos (use 24h). Dados de usuários podem ser sincronizados de hora em hora no máximo.

## URLs de Assets

```
Avatar:    https://retroachievements.org/UserPic/{username}.png
Badge:     https://retroachievements.org/Badge/{BadgeName}.png
Game Icon: https://retroachievements.org{ImageIcon}
Box Art:   https://retroachievements.org{ImageBoxArt}
Game URL:  https://retroachievements.org/game/{game_id}
User URL:  https://retroachievements.org/user/{username}
```
