<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>
    <xsl:template match="/">
        <html>
            <head>
                <title>Объекты недвижимости</title>
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
                <table>
                    <thead>
                        <tr>
                            <xsl:for-each select="/root/headers/header">
                                <th>
                                    <xsl:value-of select="."/>
                                </th>
                            </xsl:for-each>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="/root/row">
                            <tr>
                                <xsl:for-each select="./*">
                                   <td><xsl:value-of select="."/></td>
                                </xsl:for-each>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
