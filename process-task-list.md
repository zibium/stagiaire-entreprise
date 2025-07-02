---
description:
globs:
alwaysApply: false
---

# Gestion de la liste de tâches

Directives pour la gestion des listes de tâches dans les fichiers Markdown afin de suivre la progression de la réalisation d'un PRD.

## Implémentation des tâches

- **Une sous-tâche à la fois :** Ne **PAS** commencer la sous-tâche suivante sans avoir demandé la permission à l'utilisateur et qu'il ait répondu "oui" ou "o".
- **Protocole d'achèvement :**
  1. Lorsque vous terminez une **sous-tâche**, marquez-la immédiatement comme terminée en changeant `[ ]` en `[x]`.
  2. Si **toutes** les sous-tâches sous une tâche principale sont maintenant marquées `[x]`, marquez également la **tâche principale** comme terminée.
- S'arrêter après chaque sous-tâche et attendre le feu vert de l'utilisateur.

## Maintenance de la liste de tâches

1. **Mettez à jour la liste des tâches au fur et à mesure de votre travail :**

   - Marquez les tâches et sous-tâches comme terminées (`[x]`) conformément au protocole ci-dessus.
   - Ajoutez les nouvelles tâches au fur et à mesure qu'elles apparaissent.

2. **Maintenir la section « Fichiers pertinents » :**
   - Listez chaque fichier créé ou modifié.
   - Donnez à chaque fichier une description d'une ligne de son objectif.

## Instructions pour l'IA

Lors du travail avec des listes de tâches, l'IA doit :

1. Mettre régulièrement à jour le fichier de la liste de tâches après avoir terminé tout travail significatif.
2. Suivre le protocole d'achèvement :
   - Marquer chaque **sous-tâche** terminée `[x]`.
   - Marquer la **tâche principale** `[x]` une fois que **toutes** ses sous-tâches sont `[x]`.
3. Ajouter les tâches nouvellement découvertes.
4. Garder la section « Fichiers pertinents » précise et à jour.
5. Avant de commencer le travail, vérifier quelle est la prochaine sous-tâche.
6. Après avoir implémenté une sous-tâche, mettre à jour le fichier puis attendre l'approbation de l'utilisateur.
