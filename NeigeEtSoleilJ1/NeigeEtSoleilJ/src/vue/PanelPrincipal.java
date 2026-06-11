package vue;

import java.awt.*;
import javax.swing.*;

public abstract class PanelPrincipal extends JPanel {

    public PanelPrincipal(String titre) {
        this.setBounds(20, 60, 1060, 500);
        this.setBackground(Color.gray);
        this.setLayout(null);

        JLabel lbTitre = new JLabel(titre);
        lbTitre.setBounds(300, 10, 400, 20);
        Font unePolice = new Font("Arial", Font.ITALIC, 18);
        lbTitre.setFont(unePolice);
        this.add(lbTitre);

        this.setVisible(false);
    }
}
