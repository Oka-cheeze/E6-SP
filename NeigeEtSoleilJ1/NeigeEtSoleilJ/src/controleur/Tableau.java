package controleur;

import javax.swing.table.AbstractTableModel;

public class Tableau extends AbstractTableModel {

    private Object[][] donnees;
    private String[]   entetes;

    public Tableau(Object[][] donnees, String[] entetes) {
        this.donnees = donnees;
        this.entetes = entetes;
    }

    @Override
    public int getRowCount() {
        return donnees.length;
    }

    @Override
    public int getColumnCount() {
        return entetes.length;
    }

    @Override
    public Object getValueAt(int row, int col) {
        return donnees[row][col];
    }

    @Override
    public String getColumnName(int col) {
        return entetes[col];
    }

    // Rendre les cellules non éditables directement dans le tableau
    @Override
    public boolean isCellEditable(int row, int col) {
        return false;
    }

    // Actualise les données et rafraîchit l'affichage
    public void setDonnees(Object[][] donnees) {
        this.donnees = donnees;
        fireTableDataChanged();
    }
}
