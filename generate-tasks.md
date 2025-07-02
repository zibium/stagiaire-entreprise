---
description:
globs:
alwaysApply: false
---

# Règle : Génération d'une liste de tâches à partir d'un PRD

## Objectif

Guider un assistant IA pour créer une liste de tâches détaillée et étape par étape au format Markdown, basée sur un Document d'Exigences Produit (PRD) existant. La liste de tâches doit guider un développeur tout au long de l'implémentation.

## Sortie Attendue

- **Format :** Markdown (`.md`)
- **Emplacement :** `/tasks/`
- **Nom du fichier :** `tasks-[nom-du-fichier-prd].md` (par ex., `tasks-prd-edition-profil-utilisateur.md`)

## Processus

1.  **Recevoir la référence du PRD :** L'utilisateur indique à l'IA un fichier PRD spécifique.
2.  **Analyser le PRD :** L'IA lit et analyse les exigences fonctionnelles, les "user stories" et les autres sections du PRD spécifié.
3.  **Phase 1 : Générer les tâches principales :** En se basant sur l'analyse du PRD, créer le fichier et générer les tâches principales de haut niveau requises pour implémenter la fonctionnalité. Utilisez votre jugement pour déterminer le nombre de tâches de haut niveau à utiliser. Il y en aura probablement environ 5. Présentez ces tâches à l'utilisateur dans le format spécifié (sans les sous-tâches pour le moment). Informez l'utilisateur : "J'ai généré les tâches de haut niveau basées sur le PRD. Prêt à générer les sous-tâches ? Répondez par 'Go' pour continuer."
4.  **Attendre la confirmation :** Marquer une pause et attendre que l'utilisateur réponde par "Go".
5.  **Phase 2 : Générer les sous-tâches :** Une fois que l'utilisateur a confirmé, décomposer chaque tâche principale en sous-tâches plus petites et exploitables, nécessaires pour accomplir la tâche principale. S'assurer que les sous-tâches découlent logiquement de la tâche principale et couvrent les détails d'implémentation suggérés par le PRD.
6.  **Identifier les fichiers pertinents :** En se basant sur les tâches et le PRD, identifier les fichiers potentiels qui devront être créés ou modifiés. Lister ces fichiers dans la section `Fichiers pertinents`, en incluant les fichiers de test correspondants si applicable.
7.  **Générer le rendu final :** Combiner les tâches principales, les sous-tâches, les fichiers pertinents et les notes dans la structure Markdown finale.
8.  **Sauvegarder la liste de tâches :** Sauvegarder le document généré dans le répertoire `/tasks/` avec le nom de fichier `tasks-[nom-du-fichier-prd].md`, où `[nom-du-fichier-prd]` correspond au nom de base du fichier PRD d'entrée (par ex., si l'entrée était `prd-edition-profil-utilisateur.md`, la sortie sera `tasks-prd-edition-profil-utilisateur.md`).

## Format de Sortie

La liste de tâches générée _doit_ suivre cette structure :

```markdown
## Fichiers pertinents

- `chemin/vers/fichier/potentiel1.ts` - Brève description de la pertinence de ce fichier (ex: Contient le composant principal pour cette fonctionnalité).
- `chemin/vers/fichier1.test.ts` - Tests unitaires pour `fichier1.ts`.
- `chemin/vers/un/autre/fichier.tsx` - Brève description (ex: Gestionnaire de route API pour la soumission des données).
- `chemin/vers/un/autre/fichier.test.tsx` - Tests unitaires pour `un/autre/fichier.tsx`.
- `lib/utils/helpers.ts` - Brève description (ex: Fonctions utilitaires nécessaires pour les calculs).
- `lib/utils/helpers.test.ts` - Tests unitaires pour `helpers.ts`.

### Notes

- Les tests unitaires doivent généralement être placés à côté des fichiers de code qu'ils testent (par ex., `MonComposant.tsx` et `MonComposant.test.tsx` dans le même répertoire).
- Utilisez `npx jest [chemin/optionnel/vers/le/fichier/de/test]` pour lancer les tests. L'exécution sans chemin exécute tous les tests trouvés par la configuration de Jest.

## Tâches

- [ ] 1.0 Titre de la tâche principale
  - [ ] 1.1 [Description de la sous-tâche 1.1]
  - [ ] 1.2 [Description de la sous-tâche 1.2]
- [ ] 2.0 Titre de la tâche principale
  - [ ] 2.1 [Description de la sous-tâche 2.1]
- [ ] 3.0 Titre de la tâche principale (peut ne pas nécessiter de sous-tâches si purement structurel ou de configuration)
```

## Modèle d'Interaction

Le processus exige explicitement une pause après la génération des tâches principales pour obtenir la confirmation de l'utilisateur ("Go") avant de procéder à la génération des sous-tâches détaillées. Cela garantit que le plan de haut niveau correspond aux attentes de l'utilisateur avant d'entrer dans les détails.

## Public Cible

Supposer que le lecteur principal de la liste de tâches est un **développeur junior** qui implémentera la fonctionnalité.
