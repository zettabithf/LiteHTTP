using System;
using System.Text;
using System.Drawing;
using System.Windows.Forms;
using System.Drawing.Drawing2D;
using System.Collections.Generic;

namespace LiteHTTP_Builder
{
    class CustomLabel : Label
    {
        public CustomLabel()
        {
            this.AutoSize = false;
            this.Size = new Size(3, 90);
            this.BackColor = Color.Transparent;
        }

        protected override void OnPaint(PaintEventArgs e)
        {
            Bitmap B = new Bitmap(Width, Height);
            Graphics G = Graphics.FromImage(B);
            G.DrawLine(Pens.Black, 1, 1, 1, Height - 1);
            e.Graphics.DrawImage((Image)B.Clone(), 0, 0);
            G.Dispose();
            B.Dispose();
        }
    }
}
