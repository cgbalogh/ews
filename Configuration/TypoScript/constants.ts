
plugin.tx_ews_ews {
    view {
        # cat=plugin.tx_ews_ews/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:ews/Resources/Private/Templates/
        # cat=plugin.tx_ews_ews/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:ews/Resources/Private/Partials/
        # cat=plugin.tx_ews_ews/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:ews/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_ews_ews//a; type=string; label=Default storage PID
        storagePid =
    }
}
