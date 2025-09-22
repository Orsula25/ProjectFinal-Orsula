# Contexte du projet

L’application vise à aider les petits commerçants et gérants de boutiques à gérer efficacement leurs stocks et leurs finances.
Elle doit être simple d’utilisation, intuitive, et adaptée à différents profils d’utilisateurs (gérants et vendeurs).
À terme, l’application intégrera de l’intelligence artificielle pour faciliter la prise de décision grâce à la prédiction des ventes et aux recommandations de réapprovisionnement.

Une première version d’interface a été réalisée avec Figma: https://www.figma.com/design/KxTJVfrm3JZk7KmCqYW86H/G%C3%A9stion-de-Commerce?node-id=0-1&p=f&t=d5J2sedFD8aDUhv8-0

## Objectifs du projet

Offrir aux gérants un outil complet de gestion des stocks et des finances.

Permettre aux vendeurs de gérer uniquement les stocks (ventes et mouvements de produits).

Fournir une interface ergonomique.

Générer des rapports financiers clairs pour le suivi de la rentabilité.

Intégrer un module d’IA pour anticiper les besoins futurs.

## Fonctionnalités principales

### Gestion des utilisateurs et des rôles

Comptes utilisateurs avec rôles distincts :

Gérant : accès complet (stocks, finances, rapports, gestion des utilisateurs).

Vendeur : accès uniquement à la gestion des stocks et enregistrement des ventes.

Création, modification et suppression de comptes par le gérant.

Authentification sécurisée : email + mot de passe, avec récupération sécurisée en cas d’oubli.

Cas d’utilisation :

Le gérant crée un compte pour le vendeur.

Le vendeur se connecte et voit uniquement la section “Stocks”.

### Gestion des stocks

Produits : ajout, modification, suppression (nom, description, catégorie, prix, quantité, seuil de réapprovisionnement).

Mouvements de stock :

Entrées (réapprovisionnement, nouvelle commande).

Sorties (vente d’un produit).

Alertes automatiques en cas de stock faible (notification au gérant et au vendeur).

Cas d’utilisation :

Un vendeur enregistre une vente, le stock est mis à jour automatiquement.

Le gérant reçoit une alerte lorsque le stock d’un produit descend sous le seuil critique.

### Gestion des finances

Suivi des recettes et dépenses (ventes, achats, salaires, loyers, fournisseurs, taxes, impôts, etc…).

Rapports financiers :

Bilan (recettes/dépenses sur une période).

Compte de résultat (bénéfice net).

Graphiques pour visualiser l’évolution des finances.

Alertes financières : notification si les dépenses dépassent un seuil défini.

Cas d’utilisation :

Le gérant entre une dépense fournisseur et voit immédiatement l’impact sur les finances.

Il consulte un rapport mensuel qui présente recettes, dépenses et bénéfice net.

### Module d’Intelligence Artificielle (IA)

Prédiction des ventes à partir des données passées (historique, saisonnalité, tendances).

Recommandations de réapprovisionnement basées sur les prévisions et le stock actuel.

Analyse des tendances : identification des produits les plus et les moins populaires.

Cas d’utilisation :

L’IA prédit une hausse des ventes d’un produit et recommande un réapprovisionnement avant rupture.

### Gestion des factures

Facturation automatique après chaque vente (date, produits, quantités, prix total, taxes).

Consultation et exportation en PDF des factures par le gérant.

Suivi des paiements (facture payée/non payée) avec possibilité de rappel client.

Cas d’utilisation :

Après une vente, une facture est générée automatiquement.

Le gérant télécharge les factures réglées au format PDF pour archivage.

## Technologies à utiliser

Application web responsive (PC, tablette, smartphone).

Base de données relationnelle (MySQL).

Backend en PHP/Symfony .

Interface simple et claire (HTML/CSS/JS, Bootstrap).

IA (Python, API, Machine Learning).

## Bénéfices attendus

Gain de temps pour la gestion quotidienne.

Réduction des erreurs liées aux stocks et aux finances.

Meilleure visibilité pour la prise de décision grâce aux rapports et à l’IA.
