See [trees_text.txt](trees.txt) file.

The text has three distinct topics that a neural chunker should separate:

1. Environmental importance of trees (ecological benefits, biodiversity, climate change)
2. History of Namibia (pre-colonial era, German colonization, South African rule, independence)
3. Medicinal trees (historical significance, pharmaceutical applications, biodiversity)

This is ideal for testing because:
- Topics are clearly distinct
    - The first and third topics are somewhat related (both about trees),\
      but separated by a completely different topic (Namibia history)
- Each section has natural subsections (Environmental Benefits, Economic Importance, etc.)
- The chunker should identify major topic boundaries and potentially break on subsection boundaries too

Sources:
- https://github.com/rmartinshort/text_chunking - source of the test dataset
- https://towardsdatascience.com/a-visual-exploration-of-semantic-text-chunking-6bb46f728e30/ - methodology reference                               
