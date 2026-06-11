package vue;

import java.awt.Color;
import java.awt.Font;
import java.awt.GridLayout;
import java.awt.Image;
import java.awt.event.*;
import javax.swing.*;

import controleur.Controleur;
import controleur.NeigeEtSoleil;
import controleur.User;

public class VueConnexion extends JFrame implements ActionListener, KeyListener {

    private JTextField     txtEmail  = new JTextField("admin@neige.fr");
    private JPasswordField txtMdp    = new JPasswordField("admin123");
    private JButton        btValider = new JButton("Valider");
    private JButton        btAnnuler = new JButton("Annuler");
    private JPanel         panelForm = new JPanel();

    public VueConnexion() {
        this.setTitle("Neige et Soleil 2026");
        this.setBounds(400, 100, 820, 420);
        this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        this.setResizable(false);
        this.setLayout(null);
        this.getContentPane().setBackground(new Color(30, 60, 100));

        // ── Logo centré à gauche ─────────────────────────────
        ImageIcon icone = new ImageIcon("src/images/logo.png");
        Image imgScalee = icone.getImage().getScaledInstance(240, 186, Image.SCALE_SMOOTH);
        JLabel lbLogo = new JLabel(new ImageIcon(imgScalee));
        lbLogo.setBounds(50, 110, 240, 186);
        this.add(lbLogo);

        // Nom de l'application sous le logo
        JLabel lbNom = new JLabel("Neige et Soleil", SwingConstants.CENTER);
        lbNom.setFont(new Font("Arial", Font.BOLD, 16));
        lbNom.setForeground(Color.WHITE);
        lbNom.setBounds(50, 300, 240, 25);
        this.add(lbNom);

        JLabel lbSous = new JLabel("Administration", SwingConstants.CENTER);
        lbSous.setFont(new Font("Arial", Font.PLAIN, 12));
        lbSous.setForeground(new Color(180, 200, 220));
        lbSous.setBounds(50, 323, 240, 20);
        this.add(lbSous);
        

        // Formulaire à droite
        this.panelForm.setBounds(360, 110, 380, 170);
        this.panelForm.setLayout(new GridLayout(3, 2, 5, 5));
        this.panelForm.setBackground(Color.gray);

        this.panelForm.add(new JLabel("Email : "));
        this.panelForm.add(this.txtEmail);

        this.panelForm.add(new JLabel("MDP : "));
        this.panelForm.add(this.txtMdp);

        this.panelForm.add(this.btAnnuler);
        this.panelForm.add(this.btValider);

        this.add(this.panelForm);

        this.btAnnuler.addActionListener(this);
        this.btValider.addActionListener(this);
        this.txtEmail.addKeyListener(this);
        this.txtMdp.addKeyListener(this);

        this.setVisible(true);
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == this.btAnnuler) viderChamps();
        else if (e.getSource() == this.btValider) traitement();
    }

    public void viderChamps() {
        this.txtEmail.setText("");
        this.txtMdp.setText("");
    }

    public void traitement() {
        String email = this.txtEmail.getText();
        String mdp   = new String(this.txtMdp.getPassword());

        if (email.equals("") || mdp.equals("")) {
            JOptionPane.showMessageDialog(this, "Veuillez remplir tous les champs !");
        } else {
            User admin = Controleur.connecterAdmin(email, mdp);
            if (admin == null) {
                JOptionPane.showMessageDialog(this, "Veuillez vérifier vos identifiants.");
            } else {
                JOptionPane.showMessageDialog(this, "Bienvenue " + admin.getPrenom() + " " + admin.getNom() + " !");
                NeigeEtSoleil.setAdminConnecte(admin);
                NeigeEtSoleil.rendreVisibleVueConnexion(false);
                NeigeEtSoleil.creerDetruireVueGeneral(true);
                PanelProfil.actualiserInfos();
            }
        }
    }

    @Override public void keyTyped   (KeyEvent e) {}
    @Override public void keyReleased(KeyEvent e) {}
    @Override public void keyPressed (KeyEvent e) {
        if (e.getKeyCode() == KeyEvent.VK_ENTER) traitement();
    }
}
