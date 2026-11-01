"""Build the two-page handoff report from the readable Markdown source."""
from pathlib import Path
from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak
from xml.sax.saxutils import escape

ROOT = Path(__file__).resolve().parent.parent
OUTPUT = ROOT / "output/pdf/estatein-development-report.pdf"
OUTPUT.parent.mkdir(parents=True, exist_ok=True)
PURPLE = colors.HexColor("#703BF7")
INK = colors.HexColor("#202027")
MUTED = colors.HexColor("#61616C")
styles = {
    "label": ParagraphStyle("label", fontName="Helvetica-Bold", fontSize=9, leading=12, textColor=PURPLE, spaceAfter=12),
    "title": ParagraphStyle("title", fontName="Helvetica-Bold", fontSize=27, leading=32, textColor=INK, spaceAfter=12),
    "sub": ParagraphStyle("sub", fontName="Helvetica", fontSize=10, leading=15, textColor=MUTED, spaceAfter=20),
    "h2": ParagraphStyle("h2", fontName="Helvetica-Bold", fontSize=12, leading=16, textColor=INK, spaceBefore=13, spaceAfter=7, keepWithNext=True),
    "body": ParagraphStyle("body", fontName="Helvetica", fontSize=9.6, leading=14, textColor=INK, spaceAfter=9, alignment=TA_LEFT),
}

def footer(canvas, doc):
    width, height = A4
    canvas.setStrokeColor(PURPLE)
    canvas.setLineWidth(2)
    canvas.line(46, height - 34, width - 46, height - 34)
    canvas.setStrokeColor(colors.HexColor("#E4E4EC"))
    canvas.setLineWidth(.5)
    canvas.line(46, 42, width - 46, 42)
    canvas.setFont("Helvetica", 8)
    canvas.setFillColor(MUTED)
    canvas.drawString(46, 28, "ESTATEIN  /  GROWMODO WORDPRESS ASSESSMENT")
    canvas.drawRightString(width - 46, 28, f"01 NOV 2026  |  {doc.page}/2")

story = [Paragraph("01 / IMPLEMENTATION", styles["label"]), Paragraph("Estatein", styles["title"]), Paragraph("Six-page custom WordPress theme<br/>Development process, architecture, and delivery notes", styles["sub"])]
sections = (ROOT / "docs/DEVELOPMENT.md").read_text().split("\n## ")[1:]
for index, section in enumerate(sections):
    title, body = section.split("\n", 1)
    if index == 3:
        story += [PageBreak(), Paragraph("02 / VERIFICATION & HANDOFF", styles["label"]), Paragraph("Built for review", styles["title"]), Paragraph("Functional checks, security boundaries, and deployment status", styles["sub"])]
    story.append(Paragraph(escape(title), styles["h2"]))
    for paragraph in body.strip().split("\n\n"):
        story.append(Paragraph(escape(paragraph.replace("\n", " ")), styles["body"]))

SimpleDocTemplate(str(OUTPUT), pagesize=A4, leftMargin=46, rightMargin=46, topMargin=54, bottomMargin=58, title="Estatein - Development Report", author="Taufiq Hidayah", subject="Growmodo custom WordPress theme assessment").build(story, onFirstPage=footer, onLaterPages=footer)
print(OUTPUT)
