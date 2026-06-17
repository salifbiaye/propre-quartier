from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import cm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.pdfgen import canvas
from reportlab.platypus import Paragraph, Table, TableStyle


ROOT = Path(__file__).resolve().parent
PDF_PATH = ROOT / "rapport-propre-quartier.pdf"
SITE_URL = "https://propre-quartier.freedev.app/"

W, H = A4
MARGIN_X = 2.1 * cm
TOP = H - 2.2 * cm
BOTTOM = 2.0 * cm
CONTENT_W = W - (2 * MARGIN_X)

GREEN = colors.HexColor("#123A2A")
GREEN_DARK = colors.HexColor("#0C2C1F")
GREEN_MID = colors.HexColor("#1C6B4B")
GOLD = colors.HexColor("#EAB308")
PAPER = colors.HexColor("#FAF8F3")
INK = colors.HexColor("#19211D")
MUTED = colors.HexColor("#5F6861")
LINE = colors.HexColor("#D8D2C6")
WASH = colors.HexColor("#ECF3EE")


def register_fonts():
    font_dir = Path("C:/Windows/Fonts")
    pdfmetrics.registerFont(TTFont("Sans", str(font_dir / "arial.ttf")))
    pdfmetrics.registerFont(TTFont("Sans-Bold", str(font_dir / "arialbd.ttf")))
    pdfmetrics.registerFont(TTFont("Serif", str(font_dir / "times.ttf")))
    pdfmetrics.registerFont(TTFont("Serif-Bold", str(font_dir / "timesbd.ttf")))


def make_styles():
    return {
        "h1": ParagraphStyle(
            "h1",
            fontName="Serif-Bold",
            fontSize=21,
            leading=25,
            textColor=GREEN,
            spaceAfter=10,
        ),
        "h2": ParagraphStyle(
            "h2",
            fontName="Sans-Bold",
            fontSize=12.5,
            leading=16,
            textColor=GREEN_MID,
            spaceAfter=7,
        ),
        "body": ParagraphStyle(
            "body",
            fontName="Sans",
            fontSize=10.4,
            leading=15.4,
            alignment=TA_JUSTIFY,
            textColor=INK,
            spaceAfter=8,
        ),
        "body_left": ParagraphStyle(
            "body_left",
            fontName="Sans",
            fontSize=10.4,
            leading=15.4,
            alignment=TA_LEFT,
            textColor=INK,
            spaceAfter=8,
        ),
        "small": ParagraphStyle(
            "small",
            fontName="Sans",
            fontSize=8.8,
            leading=12,
            textColor=MUTED,
        ),
        "table": ParagraphStyle(
            "table",
            fontName="Sans",
            fontSize=8.7,
            leading=11.5,
            textColor=INK,
        ),
        "table_bold": ParagraphStyle(
            "table_bold",
            fontName="Sans-Bold",
            fontSize=8.7,
            leading=11.5,
            textColor=INK,
        ),
        "bullet": ParagraphStyle(
            "bullet",
            fontName="Sans",
            fontSize=10.2,
            leading=14.6,
            leftIndent=13,
            firstLineIndent=-13,
            textColor=INK,
            spaceAfter=5,
        ),
    }


ST = None


def para(c, text, style_name, x, y, width=CONTENT_W, space=8):
    p = Paragraph(text, ST[style_name])
    _, h = p.wrap(width, y - BOTTOM)
    p.drawOn(c, x, y - h)
    return y - h - space


def heading(c, number, title, y):
    text = f"{number}. {title}" if number else title
    return para(c, text, "h1", MARGIN_X, y, space=12)


def subheading(c, title, y):
    return para(c, title, "h2", MARGIN_X, y, space=5)


def bullet(c, text, y, x=MARGIN_X, width=CONTENT_W):
    return para(c, f"<b>•</b>&nbsp;&nbsp;{text}", "bullet", x, y, width, space=3)


def footer(c, page_num):
    c.saveState()
    c.setFont("Sans", 8)
    c.setFillColor(colors.HexColor("#8A938D"))
    c.drawString(MARGIN_X, 1.15 * cm, "Propre Quartier - Rapport projet IDA / CMS")
    c.drawRightString(W - MARGIN_X, 1.15 * cm, f"Page {page_num}")
    c.restoreState()


def new_body_page(c, page_num):
    c.showPage()
    c.setFillColor(PAPER)
    c.rect(0, 0, W, H, stroke=0, fill=1)
    footer(c, page_num)
    return TOP


def callout(c, title, text, y):
    box_h = 2.75 * cm
    c.saveState()
    c.setFillColor(colors.white)
    c.setStrokeColor(LINE)
    c.roundRect(MARGIN_X, y - box_h, CONTENT_W, box_h, 7, stroke=1, fill=1)
    c.setFillColor(GREEN_MID)
    c.roundRect(MARGIN_X, y - box_h, 0.13 * cm, box_h, 3, stroke=0, fill=1)
    c.restoreState()
    y2 = y - 0.55 * cm
    y2 = para(c, f"<b>{title}</b>", "body_left", MARGIN_X + 0.55 * cm, y2, CONTENT_W - 1.0 * cm, space=3)
    para(c, text, "small", MARGIN_X + 0.55 * cm, y2, CONTENT_W - 1.0 * cm, space=0)
    return y - box_h - 0.55 * cm


def draw_cover(c):
    c.setFillColor(GREEN)
    c.rect(0, 0, W, H, stroke=0, fill=1)

    c.setFillColor(GREEN_MID)
    c.circle(W * 0.78, H * 0.86, 6.6 * cm, stroke=0, fill=1)

    c.setFillColor(GREEN_DARK)
    c.rect(0, 0, W, 1.0 * cm, stroke=0, fill=1)
    c.setFillColor(GOLD)
    c.rect(0, 0, W, 0.45 * cm, stroke=0, fill=1)

    x = 2.4 * cm
    y = H - 4.0 * cm

    c.setFillColor(colors.white)
    c.setFont("Sans-Bold", 11)
    c.drawString(x, y, "PROJET IDA / CMS")

    y -= 2.25 * cm
    c.setFont("Serif-Bold", 50)
    c.drawString(x, y, "Propre Quartier")

    y -= 1.05 * cm
    c.setFillColor(GOLD)
    c.setFont("Serif", 25)
    c.drawString(x, y, "le quartier reprend la parole")

    y -= 1.65 * cm
    c.setFillColor(colors.white)
    c.setFont("Sans", 13)
    c.drawString(x, y, "Plateforme citoyenne de signalement des problèmes")
    y -= 0.55 * cm
    c.drawString(x, y, "de propreté et de voirie dans les quartiers.")

    y -= 1.35 * cm
    c.setFont("Sans", 12)
    c.drawString(x, y, "CMS retenu : WordPress")
    y -= 0.55 * cm
    c.drawString(x, y, "Déploiement : InfinityFree")

    y -= 0.9 * cm
    c.setStrokeColor(GOLD)
    c.setLineWidth(2)
    c.line(x, y, x + 5.8 * cm, y)

    y -= 1.35 * cm
    c.setFont("Sans-Bold", 10)
    c.drawString(x, y, "Groupe")
    c.setFont("Sans", 11)
    for name in ["Salif Biaye", "Abdallah Moussa Diallo", "Abdoulaye Diaw"]:
        y -= 0.65 * cm
        c.drawString(x, y, name)

    c.setFont("Sans-Bold", 9.5)
    c.drawString(x, 2.3 * cm, "École Supérieure Polytechnique - UCAD")
    c.setFont("Sans", 9)
    c.drawRightString(W - x, 2.3 * cm, "Année universitaire 2025-2026")


def draw_requirements_table(c, y):
    data = [
        [
            Paragraph("<b>Demande du cahier des charges</b>", ST["table_bold"]),
            Paragraph("<b>Ce que nous avons réalisé</b>", ST["table_bold"]),
        ],
        [
            Paragraph("Identifier un besoin dans la communauté", ST["table"]),
            Paragraph("Un outil pour signaler les problèmes de propreté et de voirie du quartier.", ST["table"]),
        ],
        [
            Paragraph("Choisir le CMS/LMS adapté", ST["table"]),
            Paragraph("WordPress, car le besoin est un portail public et non une plateforme de cours.", ST["table"]),
        ],
        [
            Paragraph("Mettre en place le site web", ST["table"]),
            Paragraph("Accueil, formulaire, carte, liste des signalements, page À propos et administration.", ST["table"]),
        ],
        [
            Paragraph("Héberger sur un host gratuit", ST["table"]),
            Paragraph(f"Site mis en ligne sur InfinityFree : {SITE_URL}", ST["table"]),
        ],
        [
            Paragraph("Personnalisation", ST["table"]),
            Paragraph("Plugin métier + thème enfant codés pour le projet.", ST["table"]),
        ],
    ]
    t = Table(data, colWidths=[5.2 * cm, 11.1 * cm], hAlign="LEFT")
    t.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), WASH),
                ("GRID", (0, 0), (-1, -1), 0.35, LINE),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("TOPPADDING", (0, 0), (-1, -1), 6),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
                ("LEFTPADDING", (0, 0), (-1, -1), 7),
                ("RIGHTPADDING", (0, 0), (-1, -1), 7),
            ]
        )
    )
    _, h = t.wrap(CONTENT_W, y - BOTTOM)
    t.drawOn(c, MARGIN_X, y - h)
    return y - h - 0.65 * cm


def build_pdf():
    register_fonts()
    global ST
    ST = make_styles()

    c = canvas.Canvas(str(PDF_PATH), pagesize=A4)
    c.setTitle("Rapport - Propre Quartier")
    c.setAuthor("Groupe IDA - ESP/UCAD")

    draw_cover(c)

    page = 1
    y = new_body_page(c, page)
    y = heading(c, "01", "Présentation du projet", y)
    y = para(
        c,
        "Propre Quartier est un site WordPress qui permet aux habitants de signaler un problème "
        "dans leur quartier : ordures, fuite d'eau, éclairage en panne, voirie abîmée ou espace "
        "public laissé à l'abandon.",
        "body",
        MARGIN_X,
        y,
    )
    y = para(
        c,
        "Le but est simple : au lieu de laisser l'information se perdre dans des discussions, "
        "le site garde une trace visible, localisée et suivie. Chaque signalement peut être placé "
        "sur une carte et passer par les statuts Signalé, En cours puis Résolu.",
        "body",
        MARGIN_X,
        y,
    )
    y = callout(
        c,
        "Site mis en ligne",
        f"Adresse publique : <b>{SITE_URL}</b><br/>Le site a été vérifié après déploiement : accueil, menu, carte et signalements sont visibles.",
        y,
    )

    y = heading(c, "02", "Respect du cahier des charges", y)
    y = para(
        c,
        "Le sujet demandait de partir d'un besoin réel, de choisir un CMS ou LMS, de construire "
        "un site web, puis de l'héberger gratuitement. Notre réponse tient dans le tableau ci-dessous.",
        "body",
        MARGIN_X,
        y,
    )
    y = draw_requirements_table(c, y)

    page += 1
    y = new_body_page(c, page)
    y = heading(c, "03", "Besoin choisi", y)
    y = para(
        c,
        "Dans un quartier, beaucoup de petits problèmes deviennent gênants parce qu'ils ne sont "
        "pas signalés clairement : un dépôt d'ordures reste plusieurs jours, un lampadaire ne "
        "fonctionne plus, un caniveau déborde, un trottoir se dégrade. Notre projet répond à ce "
        "besoin concret de suivi local.",
        "body",
        MARGIN_X,
        y,
    )
    y = bullet(c, "<b>Signaler vite</b> : un habitant remplit un formulaire simple, sans compte.", y)
    y = bullet(c, "<b>Localiser</b> : le problème est placé sur une carte interactive.", y)
    y = bullet(c, "<b>Suivre</b> : le statut permet de voir si le problème est traité.", y)
    y = bullet(c, "<b>Informer</b> : les signalements restent consultables par la communauté.", y)

    y = heading(c, "04", "Pourquoi WordPress", y - 0.25 * cm)
    y = para(
        c,
        "Nous avons choisi WordPress parce que le projet est un portail citoyen. Moodle est très "
        "utile pour gérer des cours, mais notre besoin ne concerne ni classes, ni notes, ni modules "
        "d'apprentissage. WordPress donne directement les pages, les menus, les utilisateurs, les "
        "médias et une administration simple.",
        "body",
        MARGIN_X,
        y,
    )
    y = para(
        c,
        "Le vrai travail de personnalisation se trouve dans deux parties : un thème enfant pour "
        "l'apparence, et un plugin métier pour les données de signalement. Ainsi, les contenus ne "
        "dépendent pas du design : si on change de thème, les signalements restent en base.",
        "body",
        MARGIN_X,
        y,
    )

    page += 1
    y = new_body_page(c, page)
    y = heading(c, "05", "Mise en place technique", y)
    y = para(
        c,
        "Le site a d'abord été construit en local avec Docker. Le projet contient un service "
        "WordPress, une base MariaDB, phpMyAdmin et un conteneur WP-CLI. Cela permet de relancer "
        "l'installation proprement sans tout refaire à la main.",
        "body",
        MARGIN_X,
        y,
    )
    y = subheading(c, "Organisation du projet", y)
    y = bullet(c, "<b>docker-compose.yml</b> lance WordPress, MariaDB, phpMyAdmin et WP-CLI.", y)
    y = bullet(c, "<b>bin/install.sh</b> installe WordPress, active le thème, le plugin, les pages et les données de démonstration.", y)
    y = bullet(c, "<b>wp-content/plugins/propre-quartier-core</b> contient la logique métier.", y)
    y = bullet(c, "<b>wp-content/themes/propre-quartier</b> contient la présentation du site.", y)

    y = heading(c, "06", "Fonctionnalités réalisées", y - 0.25 * cm)
    y = bullet(c, "Page d'accueil personnalisée avec statistiques et boutons d'action.", y)
    y = bullet(c, "Carte interactive avec Leaflet et marqueurs colorés.", y)
    y = bullet(c, "Formulaire public pour envoyer un signalement.", y)
    y = bullet(c, "Liste et fiches détaillées des signalements.", y)
    y = bullet(c, "Statuts de suivi : Signalé, En cours, Résolu.", y)
    y = bullet(c, "Pages Accueil, Signaler, Signalements et À propos.", y)

    page += 1
    y = new_body_page(c, page)
    y = heading(c, "07", "Personnalisation du CMS", y)
    y = para(
        c,
        "Le projet ne se limite pas à installer WordPress et à choisir un thème. Nous avons créé "
        "un plugin spécifique pour gérer les signalements. Ce plugin déclare un type de contenu "
        "Signalement, des catégories de problèmes, des champs de localisation et des shortcodes "
        "pour afficher la carte, le formulaire et la liste.",
        "body",
        MARGIN_X,
        y,
    )
    y = para(
        c,
        "Le thème enfant donne une identité visuelle propre au projet : couleurs vertes liées à "
        "l'environnement, boutons de signalement, pages adaptées au mobile et gabarits dédiés aux "
        "signalements. Le menu mobile a aussi été corrigé pour être lisible avec un panneau opaque.",
        "body",
        MARGIN_X,
        y,
    )
    y = subheading(c, "Sécurité et qualité", y)
    y = bullet(c, "Les formulaires utilisent les protections WordPress contre les soumissions abusives.", y)
    y = bullet(c, "Les champs saisis sont nettoyés avant enregistrement.", y)
    y = bullet(c, "Les signalements peuvent être modérés depuis l'administration.", y)
    y = bullet(c, "Les permaliens propres facilitent la navigation et le référencement.", y)

    page += 1
    y = new_body_page(c, page)
    y = heading(c, "08", "Déploiement", y)
    y = para(
        c,
        "Pour respecter la contrainte d'un hébergement gratuit, nous avons mis le site en ligne "
        "sur InfinityFree. La migration a été faite avec All-in-One WP Migration, car cet outil "
        "exporte en un seul fichier la base, les pages, le thème, le plugin et les réglages.",
        "body",
        MARGIN_X,
        y,
    )
    y = subheading(c, "Étapes suivies", y)
    y = bullet(c, "Génération locale d'une archive .wpress depuis WordPress.", y)
    y = bullet(c, "Création d'un compte gratuit InfinityFree.", y)
    y = bullet(c, "Création du domaine propre-quartier.freedev.app.", y)
    y = bullet(c, "Installation d'un WordPress vide avec le Script Installer.", y)
    y = bullet(c, "Installation du plugin All-in-One WP Migration sur le site en ligne.", y)
    y = bullet(c, "Import de l'archive .wpress puis vérification du site public.", y)
    y = para(
        c,
        "Après l'import, les permaliens ont été enregistrés à nouveau dans l'administration WordPress. "
        "Cette étape évite les erreurs sur les URL des pages et des signalements.",
        "body",
        MARGIN_X,
        y - 0.25 * cm,
    )
    y = callout(c, "Résultat", f"Le site final est accessible à l'adresse : <b>{SITE_URL}</b>", y)

    page += 1
    y = new_body_page(c, page)
    y = heading(c, "09", "Difficultés rencontrées", y)
    y = para(
        c,
        "La principale difficulté a été de garder un site simple à présenter tout en montrant un vrai "
        "travail de développement. La solution a été de séparer clairement les responsabilités : le "
        "plugin gère les données, le thème gère l'affichage, Docker gère l'environnement local.",
        "body",
        MARGIN_X,
        y,
    )
    y = bullet(c, "Le choix WordPress/Moodle a été tranché par le besoin réel : portail citoyen, pas plateforme de cours.", y)
    y = bullet(c, "Les permissions d'export Docker ont été corrigées avec une commande `make permissions`.", y)
    y = bullet(c, "Le passage en ligne a été simplifié grâce au fichier .wpress.", y)
    y = bullet(c, "Le menu mobile a été ajusté après test sur petit écran.", y)

    y = heading(c, "10", "Répartition du travail", y - 0.2 * cm)
    y = bullet(c, "<b>Salif Biaye</b> : Docker, WP-CLI, plugin métier, export et déploiement.", y)
    y = bullet(c, "<b>Abdallah Moussa Diallo</b> : thème, pages publiques, intégration visuelle et responsive.", y)
    y = bullet(c, "<b>Abdoulaye Diaw</b> : contenus, tests, SEO, données de démonstration et rapport.", y)

    y = heading(c, "11", "Conclusion", y - 0.2 * cm)
    y = para(
        c,
        "Propre Quartier répond au cahier des charges : le besoin est concret, le CMS est justifié, "
        "le site est fonctionnel, personnalisé et hébergé gratuitement. Le projet montre aussi une "
        "bonne utilisation de WordPress, au-delà d'une simple configuration, grâce au plugin métier "
        "et au thème enfant.",
        "body",
        MARGIN_X,
        y,
    )
    callout(
        c,
        "Bilan",
        "Un quartier plus propre commence par un signalement clair. Propre Quartier rend ce geste simple, visible et suivi.",
        y,
    )

    c.save()
    print(PDF_PATH)


if __name__ == "__main__":
    build_pdf()
