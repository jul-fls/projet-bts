--
-- Base de données : `projet`
--
CREATE DATABASE IF NOT EXISTS `projet` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `projet`;

-- --------------------------------------------------------

--
-- Structure de la table `alertes`
--

CREATE TABLE `alertes` (
  `id` int(11) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `type_de_donnees` int(11) NOT NULL COMMENT '0=> co2\r\n1=>temp\r\n2=>hum',
  `type_dalerte` int(11) NOT NULL COMMENT '0=>au dessus\r\n1=>en dessous',
  `valeur_de_declenchement` float NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '0=>désactivé\r\n1=>activé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `donnees_capteurs`
--

CREATE TABLE `donnees_capteurs` (
  `id` bigint(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `co2` int(11) NOT NULL,
  `temp` float NOT NULL,
  `hum` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_temp`
--

CREATE TABLE `password_reset_temp` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `resetkey` varchar(250) NOT NULL,
  `expDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom_utilisateur` varchar(50) NOT NULL,
  `prenom_utilisateur` varchar(50) NOT NULL,
  `type_utilisateur` int(11) NOT NULL COMMENT '0: élève 1: professeur 2: formateur 3: personnel 4: autres',
  `role` int(11) NOT NULL COMMENT '	0: utilisateur 1: administrateur 2: Super Administrateur',
  `description` varchar(255) NOT NULL COMMENT 'élève => classe, prof => matière, formateur => matière, personnel => fonction, autres => description de l''utilisateur',
  `login` varchar(150) NOT NULL,
  `password_hash` varchar(256) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(10) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `alertes`
--
ALTER TABLE `alertes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `donnees_capteurs`
--
ALTER TABLE `donnees_capteurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_temp`
--
ALTER TABLE `password_reset_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`(191));

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`,`prenom_utilisateur`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `alertes`
--
ALTER TABLE `alertes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `donnees_capteurs`
--
ALTER TABLE `donnees_capteurs`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `password_reset_temp`
--
ALTER TABLE `password_reset_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
