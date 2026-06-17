from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (
    Flowable,
    KeepTogether,
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path(__file__).resolve().parent
PDF_PATH = ROOT / "rapport-propre-quartier.pdf"
SITE_URL = "https://propre-quartier.freedev.app/"
ADMIN_URL = "https://propre-quartier.freedev.app/wp-admin/"


class CoverBlock(Flowable):
    def __init__(self):
        super().__init__()
        self.width, self.height = 16.6 * cm, 24.0 * cm

    def draw(self):
        c = self.canv
        w, h = self.width, self.height
        c.saveState()
        c.setFillColor(colors.HexColor("#123A2A"))
        c.rect(0, 0, w, h, stroke=0, fill=1)
        c.setFillColor(colors.HexColor("#1C6B4B"))
        c.circle(w * 0.88, h * 0.90, 210, stroke=0, fill=1)
        c.setFillColor(colors.HexColor("#EAB308"))
        c.rect(0, 0, w, 0.72 * cm, stroke=0, fill=1)
        c.setFillColor(colors.white)
        c.setFont("DejaVuSans-Bold", 11)
        c.drawString(2.2 * cm, h - 3.1 * cm, "PROJET IDA / CMS")
        c.setFont("DejaVuSerif-Bold", 48)
        c.drawString(2.2 * cm, h - 6.0 * cm, "Propre Quartier")
        c.setFillColor(colors.HexColor("#EAB308"))
        c.setFont("DejaVuSerif", 24)
        c.drawString(2.2 * cm, h - 7.25 * cm, "le quartier reprend la parole")
        c.setFillColor(colors.white)
        c.setFont("DejaVuSans", 13)
        lines = [
            "Plateforme citoyenne de signalement des problèmes",
            "de propreté et de voirie dans les quartiers.",
            "",
            "CMS retenu : WordPress",
            "Déploiement : InfinityFree",
        ]
        y = h - 9.2 * cm
        for line in lines:
            c.drawString(2.2 * cm, y, line)
            y -= 0.68 * cm
        c.setStrokeColor(colors.HexColor("#EAB308"))
        c.setLineWidth(2)
        c.line(2.2 * cm, h - 12.7 * cm, 7.8 * cm, h - 12.7 * cm)
        c.setFont("DejaVuSans-Bold", 10)
        c.drawString(2.2 * cm, h - 14.0 * cm, "Groupe")
        c.setFont("DejaVuSans", 11)
        c.drawString(2.2 * cm, h - 14.75 * cm, "Salif Biaye")
        c.drawString(2.2 * cm, h - 15.45 * cm, "Abdallah Moussa Diallo")
        c.drawString(2.2 * cm, h - 16.15 * cm, "Abdoulaye Diaw")
        c.setFont("DejaVuSans-Bold", 10)
        c.drawString(2.2 * cm, 2.35 * cm, "École Supérieure Polytechnique - UCAD")
        c.setFont("DejaVuSans", 9)
        c.drawRightString(w - 2.2 * cm, 2.35 * cm, "Année universitaire 2025-2026")
        c.restoreState()


def register_fonts():
    font_dir = Path("C:/Windows/Fonts")
    pdfmetrics.registerFont(TTFont("DejaVuSans", str(font_dir / "arial.ttf")))
    pdfmetrics.registerFont(TTFont("DejaVuSans-Bold", str(font_dir / "arialbd.ttf")))
    pdfmetrics.registerFont(TTFont("DejaVuSerif", str(font_dir / "times.ttf")))
    pdfmetrics.registerFont(TTFont("DejaVuSerif-Bold", str(font_dir / "timesbd.ttf")))


def styles():
    base = getSampleStyleSheet()
    return {
        "title": ParagraphStyle(
            "title",
            parent=base["Title"],
            fontName="DejaVuSerif-Bold",
            fontSize=22,
            leading=27,
            textColor=colors.HexColor("#123A2A"),
            spaceAfter=12,
        ),
        "h1": ParagraphStyle(
            "h1",
            parent=base["Heading1"],
            fontName="DejaVuSerif-Bold",
            fontSize=17,
            leading=22,
            textColor=colors.HexColor("#123A2A"),
            spaceBefore=10,
            spaceAfter=7,
        ),
        "h2": ParagraphStyle(
            "h2",
            parent=base["Heading2"],
            fontName="DejaVuSans-Bold",
            fontSize=12,
            leading=16,
            textColor=colors.HexColor("#14533A"),
            spaceBefore=7,
            spaceAfter=4,
        ),
        "body": ParagraphStyle(
            "body",
            parent=base["BodyText"],
            fontName="DejaVuSans",
            fontSize=9.6,
            leading=14,
            alignment=TA_JUSTIFY,
            spaceAfter=6,
        ),
        "small": ParagraphStyle(
            "small",
            parent=base["BodyText"],
            fontName="DejaVuSans",
            fontSize=8.2,
            leading=11,
            textColor=colors.HexColor("#4B5563"),
        ),
        "center": ParagraphStyle(
            "center",
            parent=base["BodyText"],
            fontName="DejaVuSans",
            fontSize=9.6,
            leading=14,
            alignment=TA_CENTER,
            spaceAfter=5,
        ),
        "toc": ParagraphStyle(
            "toc",
            parent=base["BodyText"],
            fontName="DejaVuSans",
            fontSize=10.5,
            leading=16,
            leftIndent=10,
            firstLineIndent=-10,
        ),
        "table": ParagraphStyle(
            "table",
            parent=base["BodyText"],
            fontName="DejaVuSans",
            fontSize=8.2,
            leading=11,
            alignment=TA_LEFT,
        ),
        "table_bold": ParagraphStyle(
            "table_bold",
            parent=base["BodyText"],
            fontName="DejaVuSans-Bold",
            fontSize=8.2,
            leading=11,
            alignment=TA_LEFT,
        ),
    }


def p(text, style):
    return Paragraph(text, style)


def bullets(items, st):
    return ListFlowable(
        [ListItem(p(item, st["body"]), leftIndent=8) for item in items],
        bulletType="bullet",
        leftIndent=14,
        bulletColor=colors.HexColor("#1C6B4B"),
        bulletFontName="DejaVuSans-Bold",
    )


def table(data, widths=None):
    st = styles()
    formatted = []
    for r, row in enumerate(data):
        formatted.append([p(str(cell), st["table_bold"] if r == 0 else st["table"]) for cell in row])
    t = Table(formatted, colWidths=widths, repeatRows=1, hAlign="LEFT")
    t.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#ECF3EE")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.HexColor("#123A2A")),
                ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#D8D2C6")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 6),
                ("RIGHTPADDING", (0, 0), (-1, -1), 6),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
            ]
        )
    )
    return t


def box(title, text, st, color="#1C6B4B"):
    data = [[p(f"<b>{title}</b>", st["table_bold"])], [p(text, st["table"])]]
    t = Table(data, colWidths=[16.6 * cm])
    t.setStyle(
        TableStyle(
            [
                ("BOX", (0, 0), (-1, -1), 0.7, colors.HexColor("#D8D2C6")),
                ("LINEBEFORE", (0, 0), (0, -1), 4, colors.HexColor(color)),
                ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#FBFAF6")),
                ("TOPPADDING", (0, 0), (-1, -1), 7),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 7),
                ("LEFTPADDING", (0, 0), (-1, -1), 10),
                ("RIGHTPADDING", (0, 0), (-1, -1), 10),
            ]
        )
    )
    return t


def header_footer(canvas, doc):
    if doc.page == 1:
        return
    canvas.saveState()
    canvas.setFont("DejaVuSans", 8)
    canvas.setFillColor(colors.HexColor("#8A938D"))
    canvas.drawString(1.65 * cm, 1.05 * cm, "Propre Quartier - Rapport projet IDA / CMS")
    canvas.drawRightString(A4[0] - 1.65 * cm, 1.05 * cm, f"Page {doc.page - 1}")
    canvas.restoreState()


def build_story():
    st = styles()
    story = [CoverBlock(), PageBreak()]

    story += [
        p("Sommaire", st["title"]),
        p("01. Introduction et contexte", st["toc"]),
        p("02. Cahier des charges et réponse du projet", st["toc"]),
        p("03. Identification du besoin communautaire", st["toc"]),
        p("04. Choix du CMS : WordPress", st["toc"]),
        p("05. Architecture technique et organisation du code", st["toc"]),
        p("06. Fonctionnalités réalisées", st["toc"]),
        p("07. Niveau de personnalisation", st["toc"]),
        p("08. Sécurité, qualité et SEO", st["toc"]),
        p("09. Déploiement local et hébergement gratuit", st["toc"]),
        p("10. Difficultés rencontrées et solutions", st["toc"]),
        p("11. Répartition des tâches", st["toc"]),
        p("12. Conclusion et perspectives", st["toc"]),
        PageBreak(),
    ]

    story += [
        p("01. Introduction et contexte", st["h1"]),
        p(
            "Ce rapport présente <b>Propre Quartier</b>, une plateforme citoyenne de signalement "
            "des problèmes de propreté et de voirie. Le projet a été réalisé dans le cadre du module "
            "<b>IDA / CMS</b> avec un objectif clair : partir d'un besoin réel de la communauté, "
            "choisir un CMS adapté, construire un site fonctionnel et l'héberger gratuitement.",
            st["body"],
        ),
        p(
            "L'idée n'est pas de produire un simple site vitrine. Le site permet à un habitant de "
            "signaler un dépôt sauvage, une fuite d'eau, un lampadaire en panne, un nid-de-poule ou "
            "un espace vert abandonné, puis de suivre l'état de traitement du problème.",
            st["body"],
        ),
        box(
            "Adresse publique du site",
            f"Site en ligne : <b>{SITE_URL}</b><br/>Administration WordPress : <b>{ADMIN_URL}</b>",
            st,
        ),
        p("02. Cahier des charges et réponse du projet", st["h1"]),
        table(
            [
                ["Exigence du cahier des charges", "Réponse dans Propre Quartier"],
                ["Identifier un besoin dans la communauté", "Besoin choisi : signaler et suivre les problèmes de propreté et de voirie dans un quartier."],
                ["Choisir le CMS/LMS adapté", "WordPress retenu pour un portail public, extensible, facile à administrer et compatible avec l'hébergement gratuit."],
                ["Mettre en place le site web", "Site complet : accueil, formulaire, carte, liste filtrable, pages de contenu, admin WordPress."],
                ["Héberger le site web sur un host gratuit", f"Déploiement réalisé sur InfinityFree : {SITE_URL}"],
                ["Originalité du projet", "Projet civic-tech utile, pas une simple vitrine : il collecte, localise et suit des signalements."],
                ["Niveau de personnalisation", "Plugin métier et thème enfant développés spécifiquement pour le projet."],
            ],
            [5.2 * cm, 11.4 * cm],
        ),
        p("03. Identification du besoin communautaire", st["h1"]),
        p(
            "Dans les quartiers, certains problèmes visibles dans l'espace public restent longtemps "
            "sans traitement parce qu'ils ne sont pas correctement remontés : déchets accumulés, "
            "caniveaux bouchés, éclairage public défectueux, trottoirs abîmés. L'information circule "
            "souvent oralement, sans localisation précise ni historique.",
            st["body"],
        ),
        bullets(
            [
                "<b>Faciliter le signalement</b> : l'habitant peut décrire le problème sans créer de compte.",
                "<b>Localiser précisément</b> : chaque signalement est placé sur une carte interactive.",
                "<b>Suivre l'évolution</b> : les statuts « Signalé », « En cours » et « Résolu » rendent l'action visible.",
                "<b>Valoriser la participation</b> : les habitants voient que leurs remontées restent consultables et utiles.",
            ],
            st,
        ),
        p("04. Choix du CMS : pourquoi WordPress", st["h1"]),
        p(
            "Nous avons comparé plusieurs options. Moodle est très adapté à l'apprentissage en ligne, "
            "mais notre besoin n'est pas un LMS : il ne s'agit pas de gérer des cours, des notes ou des classes. "
            "WordPress est mieux adapté à un portail public communautaire et permet de développer un plugin "
            "spécifique sans réinventer l'administration, les comptes, les pages et les médias.",
            st["body"],
        ),
        table(
            [
                ["Critère", "WordPress", "Moodle", "Conclusion"],
                ["Portail public", "Très adapté", "Orienté cours", "WordPress"],
                ["Extension par code", "Thèmes + plugins", "Plugins mais logique LMS", "WordPress"],
                ["Hébergement gratuit", "Facile", "Plus lourd", "WordPress"],
                ["Administration", "Simple et connue", "Spécialisée éducation", "WordPress"],
                ["Objectif du projet", "Signalements citoyens", "Formation en ligne", "WordPress"],
            ],
            [3.0 * cm, 4.4 * cm, 4.4 * cm, 4.8 * cm],
        ),
        p("05. Architecture technique et organisation du code", st["h1"]),
        p(
            "Le développement local s'appuie sur Docker Compose. Le service WordPress utilise Apache et PHP, "
            "MariaDB stocke les données, phpMyAdmin sert à l'inspection de la base, et WP-CLI automatise "
            "l'installation du site. Le coeur WordPress est dans un volume Docker ; le code développé par "
            "le groupe est versionné dans `wp-content`.",
            st["body"],
        ),
        table(
            [
                ["Élément", "Rôle"],
                ["docker-compose.yml", "Définit WordPress, MariaDB, phpMyAdmin et WP-CLI."],
                ["bin/install.sh", "Installe WordPress, active le thème et le plugin, crée les pages, le menu et les données de démonstration."],
                ["wp-content/plugins/propre-quartier-core", "Plugin métier : type de contenu, taxonomie, métadonnées, formulaire, carte et shortcodes."],
                ["wp-content/themes/propre-quartier", "Thème enfant : identité visuelle, gabarits, affichage public et responsive."],
                ["rapport/", "Dossier du rapport et du PDF final."],
            ],
            [5.2 * cm, 11.4 * cm],
        ),
        p("Modèle de données", st["h2"]),
        table(
            [
                ["Donnée", "Implémentation WordPress", "Utilité"],
                ["Signalement", "Custom Post Type", "Contient le titre et la description du problème."],
                ["Catégorie", "Taxonomie", "Classe les problèmes : ordures, eau, éclairage, voirie, espaces verts."],
                ["Latitude / longitude", "Post meta", "Permet l'affichage sur la carte."],
                ["Adresse", "Post meta", "Ajoute un repère lisible par les habitants."],
                ["Statut", "Post meta", "Suit l'avancement : signalé, en cours, résolu."],
            ],
            [4.1 * cm, 5.1 * cm, 7.4 * cm],
        ),
        p("06. Fonctionnalités réalisées", st["h1"]),
        bullets(
            [
                "<b>Page d'accueil personnalisée</b> avec présentation du projet, statistiques et appels à l'action.",
                "<b>Carte interactive Leaflet</b> affichant les signalements avec un code couleur par statut.",
                "<b>Formulaire public</b> pour signaler un problème, ajouter une description, une adresse et une position sur la carte.",
                "<b>Modération</b> : les signalements publics peuvent être validés depuis l'administration WordPress.",
                "<b>Liste des signalements</b> avec filtres par statut et par catégorie, pagination côté serveur.",
                "<b>Fiches détaillées</b> pour chaque signalement avec catégorie, statut, adresse et contenu.",
                "<b>Pages de contenu</b> : Accueil, Signaler, Signalements, À propos.",
                "<b>Données de démonstration</b> créées automatiquement pour montrer le fonctionnement.",
            ],
            st,
        ),
        p("07. Niveau de personnalisation", st["h1"]),
        p(
            "Le projet montre une personnalisation avancée de WordPress. Le thème ne contient que la "
            "présentation ; la logique métier est isolée dans un plugin. Cette séparation respecte une bonne "
            "architecture CMS : changer l'apparence ne supprime pas les signalements.",
            st["body"],
        ),
        table(
            [
                ["Personnalisation", "Preuve dans le projet"],
                ["Plugin métier maison", "propre-quartier-core.php + fichiers includes/cpt.php, meta.php, form.php, shortcodes.php."],
                ["Shortcodes personnalisés", "[pq_carte], [pq_formulaire], [pq_signalements]."],
                ["Thème enfant dédié", "Gabarits front-page.php, archive-signalement.php, single-signalement.php, page-signaler.php."],
                ["Interface responsive", "CSS du thème et du plugin adapté aux pages publiques."],
                ["Déploiement reproductible", "Docker Compose + WP-CLI + Makefile."],
            ],
            [5.0 * cm, 11.6 * cm],
        ),
        p("08. Sécurité, qualité et SEO", st["h1"]),
        bullets(
            [
                "<b>Nonces WordPress</b> pour protéger le formulaire public contre les soumissions non autorisées.",
                "<b>Assainissement</b> des champs saisis et échappement des données affichées.",
                "<b>Modération</b> des signalements pour éviter le spam public.",
                "<b>Permaliens propres</b> pour les pages et les signalements.",
                "<b>SEO technique</b> : titres, descriptions, Open Graph, Twitter Card et données structurées.",
                "<b>Accessibilité de base</b> : navigation claire, textes lisibles, boutons explicites.",
            ],
            st,
        ),
        p("09. Déploiement local et hébergement gratuit", st["h1"]),
        p("Déploiement local avec Docker", st["h2"]),
        table(
            [
                ["Étape", "Commande / action"],
                ["Configurer l'environnement", "Copier `.env.example` vers `.env` puis adapter les ports et mots de passe."],
                ["Démarrer les services", "`docker compose up -d` ou `make up`."],
                ["Installer WordPress automatiquement", "`docker compose run --rm wpcli` ou `make setup`."],
                ["Réparer les permissions d'export si nécessaire", "`make permissions`."],
                ["Exporter le site", "All-in-One WP Migration -> Export -> File."],
            ],
            [5.5 * cm, 11.1 * cm],
        ),
        p("Déploiement sur InfinityFree", st["h2"]),
        table(
            [
                ["Étape", "Détail réalisé"],
                ["Création de l'hébergement", "Compte gratuit InfinityFree créé, domaine gratuit `propre-quartier.freedev.app`."],
                ["Installation WordPress", "WordPress installé via le Script Installer / Softaculous."],
                ["Export local", "Archive `.wpress` générée depuis le WordPress local avec All-in-One WP Migration."],
                ["Import distant", "Archive importée dans WordPress en ligne pour transférer base, pages, thème, plugin et signalements."],
                ["URL finale", SITE_URL],
                ["Vérification", "Accueil, menu, carte, pages et signalements visibles en ligne."],
                ["Réglage final", "Réglages -> Permaliens -> Enregistrer pour régénérer les URLs."],
            ],
            [5.0 * cm, 11.6 * cm],
        ),
        box(
            "Pourquoi l'import `.wpress` est adapté",
            "Il transporte à la fois la base de données, les contenus, les utilisateurs, le thème, le plugin et les réglages. "
            "Cela évite une copie manuelle fragile et respecte la demande d'hébergement gratuit.",
            st,
            "#EAB308",
        ),
        p("10. Difficultés rencontrées et solutions", st["h1"]),
        table(
            [
                ["Difficulté", "Solution"],
                ["Choisir entre CMS et LMS", "Analyse du besoin : portail citoyen, donc WordPress plutôt que Moodle."],
                ["Ne pas perdre les données en changeant de thème", "Logique métier placée dans un plugin séparé du thème."],
                ["Afficher une carte sans clé payante", "Utilisation de Leaflet et OpenStreetMap/CARTO."],
                ["Créer un site démontrable rapidement", "Script WP-CLI idempotent avec données de démonstration."],
                ["Exporter depuis Docker", "Correction des permissions des dossiers All-in-One WP Migration avec `make permissions`."],
                ["Mettre en ligne gratuitement", "Hébergement InfinityFree + import `.wpress`."],
            ],
            [5.6 * cm, 11.0 * cm],
        ),
        p("11. Répartition des tâches", st["h1"]),
        table(
            [
                ["Membre", "Rôle principal", "Contributions"],
                ["Salif Biaye", "Back-end / DevOps", "Docker, WP-CLI, plugin métier, déploiement et export/import."],
                ["Abdallah Moussa Diallo", "Front-end / Design", "Thème, pages publiques, responsive, carte et intégration visuelle."],
                ["Abdoulaye Diaw", "Contenu / Qualité", "Pages, données de démonstration, tests, SEO et rapport."],
            ],
            [4.0 * cm, 4.0 * cm, 8.6 * cm],
        ),
        p("12. Conclusion et perspectives", st["h1"]),
        p(
            "Propre Quartier respecte le cahier des charges : le projet répond à un besoin réel, utilise un CMS "
            "justifié, propose un site fonctionnel et personnalisé, puis le rend accessible sur un hébergement gratuit. "
            "Le travail réalisé dépasse la simple installation WordPress : le plugin métier, le thème enfant, la carte "
            "interactive et le déploiement documenté montrent une vraie appropriation du CMS.",
            st["body"],
        ),
        p(
            "Les perspectives possibles sont nombreuses : notifications par email aux responsables, tableau de bord "
            "statistique, export open data, application mobile légère, ou gestion de comptes pour les services municipaux.",
            st["body"],
        ),
        box(
            "Bilan",
            "Un quartier plus propre commence par un signalement visible, localisé et suivi. Propre Quartier rend ce geste simple.",
            st,
        ),
    ]
    return story


def main():
    register_fonts()
    doc = SimpleDocTemplate(
        str(PDF_PATH),
        pagesize=A4,
        rightMargin=1.65 * cm,
        leftMargin=1.65 * cm,
        topMargin=1.55 * cm,
        bottomMargin=1.55 * cm,
        title="Rapport - Propre Quartier",
        author="Groupe IDA - ESP/UCAD",
    )
    doc.build(build_story(), onFirstPage=header_footer, onLaterPages=header_footer)
    print(PDF_PATH)


if __name__ == "__main__":
    main()
