import 'package:app/models/contribution.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:app/main_layout.dart';
import 'package:app/providers/contribution_provider.dart';

class ContributionPage extends StatefulWidget {
  const ContributionPage({super.key});

  @override
  State<ContributionPage> createState() => _ContributionPageState();
}

class _ContributionPageState extends State<ContributionPage> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() =>
      Provider.of<ContributionProvider>(context, listen: false).getUserContributions());
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<ContributionProvider>(context);
    final contributions = provider.contributions;

    return MainLayout(
      child: SafeArea(
        child: SingleChildScrollView(
          padding: EdgeInsets.all(16),
          child: Column(
            children: [
              Card(
                color: Colors.blueGrey,
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.wallet_giftcard, color: Colors.white),
                      SizedBox(width: 10),
                      Text(
                        'Contributions',
                        style: TextStyle(color: Colors.white),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 16),
              contributions.isEmpty
                  ? Card(
                      margin: EdgeInsets.symmetric(vertical: 10),
                      elevation: 4,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      color: Colors.amber.shade50,
                      child: Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Text(
                          "No Contributions have been registered yet.",
                          style: TextStyle(
                            fontStyle: FontStyle.italic,
                            color: Colors.black,
                          ),
                        ),
                      ),
                    )
                  : Column(
                      children: contributions.map((contribution) {
                        return Card(
                          margin: EdgeInsets.symmetric(vertical: 10),
                          elevation: 4,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                          color: Colors.amber.shade50,
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  contribution.name,
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.brown.shade800,
                                  ),
                                ),
                                SizedBox(height: 10),
                                buildRichText("📅 Date: ", getDate(contribution.createdAt)),
                                buildRichText("🏥 Relief Center: ", contribution.centerName),
                                buildRichText("📦 Quantity: ", "${contribution.quantity} ${contribution.unit}"),
                                buildRichText("📝 Description: ", contribution.description ?? 'N/A'),
                                buildRichText("🔁 Type: ", getType(contribution.type)),
                                SizedBox(height: 12),
                                Align(
                                  alignment: Alignment.centerLeft,
                                  child: ElevatedButton.icon(
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: Colors.red.shade400,
                                      foregroundColor: Colors.white,
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                    ),
                                    icon: Icon(Icons.delete, color: Colors.white),
                                    label: Text("Delete"),
                                    onPressed: () async {
                                      bool confirmed = await showDialog(
                                        context: context,
                                        builder: (context) => AlertDialog(
                                          title: Text("Are you sure?"),
                                          content: Text("This will permanently delete the contribution."),
                                          actions: [
                                            ElevatedButton(
                                              style: ElevatedButton.styleFrom(backgroundColor: Colors.blueGrey),
                                              onPressed: () => Navigator.pop(context, false),
                                              child: Text("Cancel", style: TextStyle(color: Colors.white)),
                                            ),
                                            ElevatedButton(
                                              style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                                              onPressed: () => Navigator.pop(context, true),
                                              child: Text("Delete", style: TextStyle(color: Colors.white)),
                                            ),
                                          ],
                                        ),
                                      );

                                      if (confirmed) {
                                        try {
                                          await Provider.of<ContributionProvider>(context, listen: false)
                                              .deleteContribution(contribution.id);

                                          ScaffoldMessenger.of(context).showSnackBar(
                                            SnackBar(content: Text("Contribution deleted successfully!")),
                                          );
                                        } catch (e) {
                                          ScaffoldMessenger.of(context).showSnackBar(
                                            SnackBar(content: Text("Error: Could not delete contribution")),
                                          );
                                        }
                                      }
                                    },
                                  ),
                                )
                              ],
                            ),
                          ),
                        );
                      }).toList(),
                    ),
            ],
          ),
        ),
      ),
    );
  }

  RichText buildRichText(String label, String value) {
    return RichText(
      text: TextSpan(
        style: TextStyle(color: Colors.grey.shade800),
        children: [
          TextSpan(
            text: label,
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
          TextSpan(text: value),
        ],
      ),
    );
  }
}
