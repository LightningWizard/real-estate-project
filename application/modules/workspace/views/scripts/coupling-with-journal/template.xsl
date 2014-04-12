<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
                <title>Стыковка с файлом от газеты "Позвоните"</title>
                <style type="text/css">
                    table {
                       border: 1px dotted #f6f6f6;
                       border-collapse: collapse;
                       width: 98%;
                       margin: auto;
                    }
                    th, td {
                        border: 1px solid black;
                        font-size: 14px;
                    }
                    th {
                        background-color: #f8f8f8
                    }
                    tr:nth-child(even) {
                        background-color: #f8f8f8
                    }
                    td.centered {
                        text-align: center
                    }

                </style>
            </head>
            <body>
                <div>Дата стыковки с <xsl:value-of select="/root/filters/dateFrom"/> по <xsl:value-of select="/root/filters/dateTo"/></div>
                <div>Статус: <xsl:value-of select="/root/filters/annStatus"/></div>
                <div>Источник данных: <xsl:value-of select="/root/filters/dataSource"/></div>
                <table>
                    <thead>
                        <tr>
                            <th>Объявление</th>
                            <th>Описание</th>
                        </tr>
                    </thead>
                    <tbody>
                     <xsl:for-each select="/root/row">
                         <tr>
                             <td width="55%"><xsl:value-of select="couplingunit_text"/></td>
                             <td width="45%" class="centered"><xsl:value-of select="couplingunit_description"/></td>
                         </tr>
                     </xsl:for-each>
                    </tbody>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
